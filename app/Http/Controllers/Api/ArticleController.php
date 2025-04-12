<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
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
        return response()->json(Article::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|integer',
            'categorie' => 'required|string',
            'variations' => 'required|array',  // Assurer qu'il y a des variations
            'variations.*.couleur' => 'required|string',
            'variations.*.taille' => 'required|string',
            'variations.*.stock' => 'required|integer',  // Validation pour le stock
            'variations.*.prix' => 'nullable|integer',   // Validation pour le prix, il est nullable
        ]);

        // Création de l'article
        $article = Article::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'prixPromotion' => $request->prixPromotion,
            'isPromotion' => $request->isPromotion,
            'pourcentageReduction' => $request->pourcentageReduction,
            'madeInGabon' => $request->madeInGabon,
            'boutique_id' => $request->boutique_id,
            'categorie' => $request->categorie,
        ]);

        // Création des variations pour cet article
        foreach ($request->variations as $variation) {
            $article->variations()->create([
                'couleur' => $variation['couleur'],
                'taille' => $variation['taille'],
                'stock' => $variation['stock'],  // Enregistrer le stock
                'prix' => $variation['prix'] ?? null,  // Enregistrer le prix, ou null si non spécifié
            ]);
        }

        return response()->json($article, 201);
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