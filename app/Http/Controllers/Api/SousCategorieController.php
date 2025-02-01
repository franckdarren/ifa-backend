<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SousCategorie;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    // ✅ Créer une nouvelle sous-catégorie avec gestion d'image
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'categorie_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageUrl = null;

        // ✅ Gestion de l'upload d'une image
        if ($request->hasFile('image')) {
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stockage sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('sous_categories', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $request->file('image')->store('public/sous_categories');
                $imageUrl = Storage::url($imagePath);
            }
        }

        // ✅ Créer la sous-catégorie
        $sousCategorie = SousCategorie::create([
            'nom' => $request->nom,
            'categorie_id' => $request->categorie_id,
            'image_url' => $imageUrl,
        ]);

        return response()->json($sousCategorie, 201);
    }

    // ✅ Mettre à jour une sous-catégorie avec gestion de l'image
    public function update(Request $request, $id)
    {
        $sousCategorie = SousCategorie::find($id);

        if (!$sousCategorie) {
            return response()->json(['message' => 'Sous-catégorie non trouvée'], 404);
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'categorie_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Gestion de l'upload d'une nouvelle image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image (si présente)
            if ($sousCategorie->image_url) {
                Storage::delete($sousCategorie->image_url);
            }

            // ✅ Stockage de l'image (local ou DigitalOcean Spaces)
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stockage sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('sous_categories', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $request->file('image')->store('public/sous_categories');
                $imageUrl = Storage::url($imagePath);
            }

            // Mettre à jour l'URL de l'image
            $sousCategorie->image_url = $imageUrl;
        }

        // ✅ Mise à jour de la sous-catégorie
        $sousCategorie->update($request->only(['nom', 'categorie_id']));

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
