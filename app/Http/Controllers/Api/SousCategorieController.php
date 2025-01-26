<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SousCategorie;
use App\Http\Controllers\Controller;

class SousCategorieController extends Controller
{
    // Récupérer toutes les sous-catégories
    public function index()
    {
        $sousCategories = SousCategorie::with('categorie')->get(); // Inclut la catégorie associée
        return response()->json($sousCategories);
    }

    // Récupérer une sous-catégorie spécifique par ID
    public function show($id)
    {
        $sousCategorie = SousCategorie::with('categorie')->find($id);

        if (!$sousCategorie) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        return response()->json($sousCategorie);
    }

    // Créer une nouvelle sous-catégorie
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        // Créer la sous-catégorie
        $sousCategorie = SousCategorie::create($validatedData);

        return response()->json($sousCategorie, 201);
    }

    // Mettre à jour une sous-catégorie existante
    public function update(Request $request, $id)
    {
        $sousCategorie = SousCategorie::find($id);

        if (!$sousCategorie) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        // Mettre à jour la sous-catégorie
        $sousCategorie->update($validatedData);

        return response()->json($sousCategorie);
    }

    // Supprimer une sous-catégorie
    public function destroy($id)
    {
        $sousCategorie = SousCategorie::find($id);

        if (!$sousCategorie) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        // Supprimer la sous-catégorie
        $sousCategorie->delete();

        return response()->json(['message' => 'Sous-catégorie supprimée avec succès']);
    }
}
