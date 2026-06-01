<?php

    namespace App\Http\Controllers;

    use App\Models\Animal;
    use App\Models\Elevage;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    class AnimalController extends Controller
    {
        // Liste des animaux de l'utilisateur connecté
        public function index()
        {
            $elevages = Elevage::where('user_id', Auth::id())->pluck('id');

            $animaux = Animal::whereIn('elevage_id', $elevages)
                        ->latest()
                        ->paginate(10);

            return view('animaux', compact('animaux'));
        }

        // Formulaire de création
        public function create()
        {
            $elevages = Elevage::where('user_id', Auth::id())->get();
            return view('animaux.create', compact('elevages'));
        }

        // Enregistrer un nouvel animal
        public function store(Request $request)
        {
            $request->validate([
                'elevage_id'       => 'required|exists:elevages,id',
                'nom'              => 'required|string|max:255',
                'espece'           => 'required|string|max:255',
                'race'             => 'nullable|string|max:255',
                'poids'            => 'nullable|numeric|min:0',
                'statut_sanitaire' => 'required|string|max:255',
                'date_naissance'   => 'nullable|date|before:today',
                'description'      => 'nullable|string',
                'img_url'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->all();

            // Gestion de l'image
            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('animaux', 'public');
            }

            Animal::create($data);

            return redirect()->route('animaux.index')
                            ->with('success', 'Animal ajouté avec succès !');
        }

        // Détails d'un animal
        public function show(Animal $animal)
        {
            $this->verifierProprietaire($animal);

            // Calcul automatique de l'âge
            if ($animal->date_naissance) {
                $naissance = carbon::parse($animal->date_naissance);
                $mois = $naissance->diffInMonths(Carbon::now());

                if ($mois<6) {
                    $age = 'Moins de 6 mois';
                }elseif ($mois <= 12) {
                    $age = 'Entre 6 et 12 mois';
                }else {
                    $annees = $naissance->diffInYears(carbon::now());
                    $age = 'Plus 1 an ('.$annees.' ans)';
                }
            }else {
                    $age = 'Non renseigné';
                }
        }

        // Formulaire de modification
        public function edit(Animal $animal)
        {
            $this->verifierProprietaire($animal);
            $elevages = Elevage::where('user_id', Auth::id())->get();
            return view('animaux.edit', compact('animal', 'elevages'));
        }

        // Mettre à jour un animal
        public function update(Request $request, Animal $animal)
        {
            $this->verifierProprietaire($animal);

            $request->validate([
                'elevage_id'       => 'required|exists:elevages,id',
                'nom'              => 'required|string|max:255',
                'espece'           => 'required|string|max:255',
                'race'             => 'nullable|string|max:255',
                'poids'            => 'nullable|numeric|min:0',
                'statut_sanitaire' => 'required|string|max:255',
                'date_naissance'   => 'nullable|date|before:today',
                'description'      => 'nullable|string',
                'img_url'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->all();

            // Gestion de l'image
            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('animaux', 'public');
            }

            $animal->update($data);

            return redirect()->route('animaux.index')
                            ->with('success', 'Animal modifié avec succès !');
        }

        // Supprimer un animal
        public function destroy(Animal $animal)
        {
            $this->verifierProprietaire($animal);
            $animal->delete();

            return redirect()->route('animaux.index')
                            ->with('success', 'Animal supprimé avec succès !');
        }

        // Vérifier que l'animal appartient à l'utilisateur connecté
        private function verifierProprietaire(Animal $animal)
        {
            $elevageIds = Elevage::where('user_id', Auth::id())->pluck('id');

            if (!$elevageIds->contains($animal->elevage_id)) {
                abort(403, 'Action non autorisée.');
            }
        }
    }
?>