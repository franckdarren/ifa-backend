<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\ImageArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageArticleController extends Controller
{
    // Récupérer toutes les images d'un article
    public function index($id)
    {
        $images = ImageArticle::where('article_id', $id)->get();
        return response()->json($images, 200);
    }

    // Ajouter une image pour un article
    // ✅ Ajouter une image pour un article
    public function store(Request $request, $articleId)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'couleur' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // ✅ L'image est requise ici
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier si l'article existe
        $article = Article::find($articleId);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $imageUrl = null;

        // ✅ Stocker l'image (Local ou DigitalOcean Spaces)
        if ($request->hasFile('image')) {
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stockage sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('image_articles', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $request->file('image')->store('public/image_articles');
                $imageUrl = Storage::url($imagePath);
            }
        }

        // ✅ Création de l'image de l'article
        $image = ImageArticle::create([
            'article_id' => $articleId,
            'couleur' => $request->couleur,
            'url_photo' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Image ajoutée avec succès !',
            'image' => $image
        ], 201);
    }

    public function show($id)
    {
        $image = ImageArticle::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        return response()->json($image, 200);
    }

    // Mettre à jour une image
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

    // Supprimer une image
    public function destroy($id)
    {
        $image = ImageArticle::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        $image->delete();
        return response()->json(['message' => 'Image supprimée avec succès'], 200);
    }
}
