<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SousCategorie;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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

        // Stocker l'image et récupérer le chemin
        if ($request->hasFile('image')) {
            // Stocker l'image dans storage/app/public/sous_categories
            $imagePath = $request->file('image')->store('public/sous_categories');

            // Générer l'URL accessible publiquement
            $imageUrl = Storage::url($imagePath);
        }

        // Avec DigitalOcean Space
        // if ($request->hasFile('image')) {
        //     // Stocker dans DigitalOcean Spaces
        //     $imagePath = $request->file('image')->store('sous_categories', 'spaces');

        //     // Générer une URL complète de l'image
        //     $imageUrl = Storage::disk('spaces')->url($imagePath);
        // }

        // Créer la sous-catégorie
        $sousCategorie = SousCategorie::create([
            'nom' => $request->nom,
            'categorie_id' => $request->categorie_id,
            'image_url' => $imageUrl ?? null,
        ]);

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
