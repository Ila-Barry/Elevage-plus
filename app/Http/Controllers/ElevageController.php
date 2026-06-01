<?php

namespace App\Http\Controllers;

use App\Models\Elevage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElevageController extends Controller
    {
        // Liste des élevages de l'utilisateur connecté
        public function index()
        {
            $elevages = Elevage::where('user_id', Auth::id())
                        ->latest()
                        ->paginate(10);

            return view('elevages', compact('elevages'));
        }

        // Formulaire de création
        public function create()
        {
            return view('elevages.create');
        }

        // Enregistrer un nouvel élevage
        public function store(Request $request)
        {
            $request->validate([
                'nom'          => 'required|string|max:255',
                'localisation' => 'required|string|max:255',
                'superficie'   => 'required|integer|min:1',
                'type_elevage' => 'required|string|max:255',
                'description'  => 'nullable|string',  //On peut mettre required à la place de nullable si necèssaire.
                'img_url'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  //L'insertion d'une image reste facultative.
            ]);
                //récupération des données.
            $data = $request->all();
            $data['user_id'] = Auth::id(); // Je récupère l'ID du propriétaire qui a ajouté l'image.

            // Gestion de l'image
            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('elevages', 'public');
            }

            Elevage::create($data);
            
            return redirect()->route('elevages.index')
                            ->with('success', 'Élevage créé avec succès !');
        }

        // Détails d'un élevage
        public function show(Elevage $elevage)
        {
            $this->verifierProprietaire($elevage);
            return view('elevages.show', compact('elevage'));
        }

        // Formulaire de modification
        public function edit(Elevage $elevage)
        {
            $this->verifierProprietaire($elevage);
            return view('elevages.edit', compact('elevage'));
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

            $data = $request->all();

            if ($request->hasFile('img_url')) {
                $data['img_url'] = $request->file('img_url')
                                    ->store('elevages', 'public');
            }

            $elevage->update($data);

            return redirect()->route('elevages.index')
                            ->with('success', 'Élevage modifié avec succès !');
        }

        // Supprimer un élevage
        public function destroy(Elevage $elevage)
        {
            $this->verifierProprietaire($elevage);
            $elevage->delete();

            return redirect()->route('elevages.index')
                            ->with('success', 'Élevage supprimé avec succès !');
        }

        // Vérifier que l'élevage appartient à l'utilisateur connecté
        private function verifierProprietaire(Elevage $elevage)
        {
            if ($elevage->user_id !== Auth::id()) {
                abort(403, 'Action non autorisée.');
            }
        }
    }
?>