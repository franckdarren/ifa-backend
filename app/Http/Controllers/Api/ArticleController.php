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
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|integer|min:0',
            'prixPromotion' => 'nullable|integer|min:0',
            'isPromotion' => 'boolean',
            'pourcentageReduction' => 'nullable|integer|min:0|max:100',
            'madeInGabon' => 'boolean',
            'boutique_id' => 'required|exists:boutiques,id',
            'categorie' => 'required|string|max:255',
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'variations' => 'required|array|min:1',
            'variations.*.taille' => 'required|string',
            'variations.*.couleur' => 'required|string',
            'variations.*.stock' => 'required|integer|min:0',
            'variations.*.price' => 'required|numeric',
            'variations.*.image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_principale')) {
            $path = $request->file('image_principale')->store('articles', 'public');
            $validatedData['image_principale'] = $path;
        }

        $article = Article::create($validatedData);

        foreach ($validatedData['variations'] as $variationData) {
            $imagePath = null;
            if (isset($variationData['image'])) {
                $imagePath = $variationData['image']->store('variations', 'public');
            }

            $article->variations()->create([
                'taille' => $variationData['taille'],
                'couleur' => $variationData['couleur'],
                'stock' => $variationData['stock'],
                'price' => $variationData['price'],
                'image' => $imagePath,
            ]);
        }

        return response()->json($article->load('variations'), 201);
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