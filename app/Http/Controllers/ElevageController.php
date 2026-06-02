<?php

    namespace App\Http\Controllers;

    use App\Models\Elevage;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class ElevageController extends Controller
    {
        // Liste des élevages
        public function index()
        {
            $elevages = Elevage::latest()->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $elevages
            ], 200);
        }

        // Enregistrer un nouvel élevage
        public function store(Request $request)
        {
            $request->validate([
                'nom'          => 'required|string|max:255',
                'localisation' => 'required|string|max:255',
                'superficie'   => 'required|integer|min:1',
                'type_elevage' => 'required|string|max:255',
                'description'  => 'nullable|string',
                'img_url'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->only([
                'nom',
                'localisation',
                'superficie',
                'type_elevage',
                'description'
            ]);

            $data['user_id'] = Auth::id();

            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('elevages', 'public');
            }

            $elevage = Elevage::create($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Élevage créé avec succès',
                'data'    => $elevage
            ], 201);
        }

        // Détails d'un élevage
        public function show(Elevage $elevage)
        {
            return response()->json([
                'status' => 'success',
                'data' => $elevage
            ], 200);
        }

        // Mettre à jour un élevage
        public function update(Request $request, Elevage $elevage)
        {
            $this->verifierProprietaire($elevage);

            $request->validate([
                'nom'          => 'required|string|max:255',
                'localisation' => 'required|string|max:255',
                'superficie'   => 'required|integer|min:1',
                'type_elevage' => 'required|string|max:255',
                'description'  => 'nullable|string',
                'img_url'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $data = $request->only([
                'nom',
                'localisation',
                'superficie',
                'type_elevage',
                'description'
            ]);

            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('elevages', 'public');
            }

            $elevage->update($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Élevage modifié avec succès',
                'data'    => $elevage
            ], 200);
        }

        // Supprimer un élevage
        public function destroy(Elevage $elevage)
        {
            $this->verifierProprietaire($elevage);
            $elevage->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Élevage supprimé avec succès'
            ], 200);
        }

        // Vérifier que l'élevage appartient à l'utilisateur connecté
        private function verifierProprietaire(Elevage $elevage)
        {
            if (Auth::user()->role === 'admin') {
                return;
            }

            if ($elevage->user_id !== Auth::id()) {
                response()->json([
                    'status'  => 'error',
                    'message' => 'Action non autorisée.'
                ], 403)->throwResponse();
            }
        }
    }
?>