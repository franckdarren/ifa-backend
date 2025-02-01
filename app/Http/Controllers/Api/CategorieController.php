<?php

namespace App\Http\Controllers\Api;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // Image optionnelle
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageUrl = null;

        // ✅ Gestion de l'upload de l'image (Stockage local ou DigitalOcean Spaces)
        if ($request->hasFile('image')) {
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stockage sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('categories', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $request->file('image')->store('public/categories');
                $imageUrl = Storage::url($imagePath);
            }
        }

        // 🔥 Création de la catégorie
        $categorie = Categorie::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'image_url' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Catégorie créée avec succès !',
            'categorie' => $categorie
        ], 201);
    }

    // ✅ Mettre à jour une catégorie existante avec gestion de l'image
    public function update(Request $request, $id)
    {
        $categorie = Categorie::find($id);
        if (!$categorie) {
            return response()->json(['message' => 'Catégorie non trouvée'], 404);
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image optionnelle
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Gestion de l'upload d'une nouvelle image
        if ($request->hasFile('image')) {
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stocker sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('categories', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $request->file('image')->store('public/categories');
                $imageUrl = Storage::url($imagePath);
            }

            // 🗑️ Supprimer l'ancienne image
            if ($categorie->image_url) {
                Storage::delete($categorie->image_url);
            }

            $categorie->image_url = $imageUrl;
        }

        // ✅ Mise à jour des autres champs
        $categorie->update($request->except('image'));

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès !',
            'categorie' => $categorie
        ], 200);
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
