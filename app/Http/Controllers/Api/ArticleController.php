<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\ImageArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with(['images', 'variations'])->get();
        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|integer',
            'categorie' => 'required|string',
            'boutique_id' => 'required|integer',

            // Image principale
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',

            // Galerie d'images
            'article_images.*' => 'image|mimes:jpeg,png,jpg|max:5120',

            // Variations JSON (les images sont envoyées séparément)
            'variations' => 'nullable|string', // JSON string
        ]);

        $isPromotion = filter_var($request->isPromotion, FILTER_VALIDATE_BOOLEAN);
        $madeInGabon = filter_var($request->madeInGabon, FILTER_VALIDATE_BOOLEAN);

        // ✅ Image principale
        $imagePrincipalePath = null;
        if ($request->hasFile('image_principale')) {
            $imagePrincipalePath = $request->file('image_principale')->store('articles', 'public');
        }

        // ✅ Création de l’article
        $article = Article::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'prixPromotion' => $request->prixPromotion,
            'isPromotion' => $isPromotion,
            'pourcentageReduction' => $request->pourcentageReduction,
            'madeInGabon' => $madeInGabon,
            'boutique_id' => $request->boutique_id,
            'categorie' => $request->categorie,
            'image_principale' => $imagePrincipalePath,
        ]);


        // ✅ Variations (fichier séparé : variation_images_0, etc.)
        if ($request->filled('variations')) {
            $variations = json_decode($request->variations, true);

            foreach ($variations as $index => $variationData) {
                $variationImagePath = null;

                // Vérifie si un fichier 'variation_images_{index}' existe
                $fileKey = "variation_images_$index";
                if ($request->hasFile($fileKey)) {
                    $variationImagePath = $request->file($fileKey)->store('variations', 'public');
                }

                $article->variations()->create([
                    'couleur' => $variationData['couleur'],
                    'taille' => $variationData['taille'],
                    'stock' => $variationData['stock'],
                    'prix' => $variationData['prix'] ?? null,
                    'image' => $variationImagePath,
                ]);
            }
        }

        return response()->json($article->load('variations', 'images'), 201);
    }






    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::with(['images', 'variations.images'])->find($id);

        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        return response()->json($article, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'prixPromotion' => 'required|integer',

            'type' => 'required|integer',
            'carateristique' => 'required|integer',

            'isDisponible' => 'required|boolean',
            'isPromotion' => 'required|boolean',
            'pourcentageReduction' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $article->update($request->all());
        return response()->json($article, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $article->delete();
        return response()->json(['message' => 'Article supprimé avec succès'], 200);
    }

    // Récupérer les articles d'une boutique
    public function articlesBoutique(string $id)
    {
        return response()->json(
            Article::where('boutique_id', $id)
                ->with(['variations.stocks', 'boutique', 'sousCategorie', 'images', 'variations.images'])
                ->get(),
            200
        );
    }

    // Récupérer les articles disponibles
    public function articlesDisponibles(string $id)
    {
        $articles = Article::whereHas('variations.stocks', function ($query) {
            $query->where('stock', '>=', 1);
        })->get();

        return response()->json($articles);
    }

}