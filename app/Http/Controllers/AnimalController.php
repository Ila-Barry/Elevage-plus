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
        // Liste des animaux
        public function index()
        {
            $animaux = Animal::latest()->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $animaux
            ], 200);
        }

        // Enregistrer un nouvel animal
        public function store(Request $request)
        {
            $request->validate([
                'elevage_id'       => 'required|exists:elevages,id',
                'nom'              => 'required|string|max:255',
                'espece'           => 'required|string|max:255',
                'race'             => 'required|string|max:255',
                'poids'            => 'required|numeric|min:0',
                'statut_sanitaire' => 'required|string|max:255',
                'date_naissance'   => 'required|date|before:today',
                'description'      => 'nullable|string',
                'img_url'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $elevage = Elevage::findOrFail($request->elevage_id);
                if (
                    Auth::user()->role !== 'admin'
                    && $elevage->user_id !== Auth::id()
                ) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Action non autorisée.'
                    ], 403);
                }
                {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Action non autorisée.'
                    ], 403);
                }

            $data = $request->only([
                'elevage_id',
                'nom',
                'espece',
                'race',
                'poids',
                'statut_sanitaire',
                'date_naissance',
                'description'
            ]);

            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('animaux', 'public');
            }

            $animal = Animal::create($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Animal ajouté avec succès',
                'data'    => $animal
            ], 201);
        }

        // Détails d'un animal
        public function show(Animal $animal)
        {
            // Calcul de l'âge
            if ($animal->date_naissance) {
                $naissance = Carbon::parse($animal->date_naissance);
                $mois = $naissance->diffInMonths(Carbon::now());

                if ($mois < 6) {
                    $age = 'Moins de 6 mois';
                } elseif ($mois <= 12) {
                    $age = 'Entre 6 et 12 mois';
                } else {
                    $annees = $naissance->diffInYears(Carbon::now());
                    $age = 'Plus de 1 an (' . $annees . ' ans)';
                }
            } else {
                $age = 'Non renseigné';
            }

            return response()->json([
                'status' => 'success',
                'data'   => $animal,
                'age'    => $age
            ], 200);
        }
        // Mettre à jour un animal
        public function update(Request $request, Animal $animal)
        {
            $this->verifierProprietaire($animal);

            $request->validate([
                'elevage_id'       => 'required|exists:elevages,id',
                'nom'              => 'required|string|max:255',
                'espece'           => 'required|string|max:255',
                'race'             => 'required|string|max:255',
                'poids'            => 'required|numeric|min:0',
                'statut_sanitaire' => 'required|string|max:255',
                'date_naissance'   => 'required|date|before:today',
                'description'      => 'nullable|string',
                'img_url'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->only([
                'elevage_id',
                'nom',
                'espece',
                'race',
                'poids',
                'statut_sanitaire',
                'date_naissance',
                'description'
            ]);

            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('animaux', 'public');
            }

            $elevage = Elevage::findOrFail($request->elevage_id);

            if (
                Auth::user()->role !== 'admin'
                && $elevage->user_id !== Auth::id()
            ) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Action non autorisée.'
                ], 403);
            }

            $animal->update($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Animal modifié avec succès',
                'data'    => $animal
            ], 200);
        }

        // Supprimer un animal
        public function destroy(Animal $animal)
        {
            $this->verifierProprietaire($animal);
            $animal->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Animal supprimé avec succès'
            ], 200);
        }

        // Vérifier que l'animal appartient à l'utilisateur connecté
        private function verifierProprietaire(Animal $animal)
        {
            if (Auth::user()->role === 'admin') {
                return;
            }

            $elevageIds = Elevage::where('user_id', Auth::id())->pluck('id');

            if (!$elevageIds->contains($animal->elevage_id)) {
                response()->json([
                    'status'  => 'error',
                    'message' => 'Action non autorisée.'
                ], 403)->throwResponse();
            }
        }
    }
?>