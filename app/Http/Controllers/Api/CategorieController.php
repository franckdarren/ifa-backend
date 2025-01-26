<?php

namespace App\Http\Controllers\Api;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorieController extends Controller
{
    // Récupérer toutes les catégories
    public function index()
    {
        $categories = Categorie::all();  // Récupère toutes les catégories
        return response()->json($categories);
    }

    // Récupérer une catégorie par son ID
    public function show($id)
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        return response()->json($categorie);
    }

    // Créer une nouvelle catégorie
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Créer la catégorie
        $categorie = Categorie::create($validatedData);

        return response()->json($categorie, 201);
    }

    // Mettre à jour une catégorie existante
    public function update(Request $request, $id)
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        // Validation des données
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Mettre à jour la catégorie
        $categorie->update($validatedData);

        return response()->json($categorie);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        // Supprimer la catégorie
        $categorie->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }
}
