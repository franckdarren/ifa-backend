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
    public function store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'url_photo' => 'required|string',
            'couleur' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $image = Article::find($id);
        if (!$image) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        // Stocker l'image et récupérer le chemin
        if ($request->hasFile('image')) {
            // Stocker l'image dans storage/app/public/image_articles
            $imagePath = $request->file('image')->store('public/image_articles');

            // Générer l'URL accessible publiquement
            $imageUrl = Storage::url($imagePath);
        }

        // Avec DigitalOcean Space
        // if ($request->hasFile('image')) {
        //     // Stocker dans DigitalOcean Spaces
        //     $imagePath = $request->file('image')->store('image_articles', 'spaces');

        //     // Générer une URL complète de l'image
        //     $imageUrl = Storage::disk('spaces')->url($imagePath);
        // }

        // Créer la catégorie
        $image = ImageArticle::create([
            'couleur' => $request->couleur,
            'article_id' => $request->article_id,

            'url_photo' => $imageUrl ?? null,
        ]);

        return response()->json($image, 201);
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'url_photo' => 'required|string',
            'couleur' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $image = ImageArticle::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        $image->url_photo = $request->url_photo;
        $image->couleur = $request->couleur;
        $image->save();

        return response()->json($image, 200);
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
