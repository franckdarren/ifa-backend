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
        // Transformation des champs is_promotion et is_made_in_gabon en booléens
        $request->merge([
            'is_promotion' => filter_var($request->input('is_promotion'), FILTER_VALIDATE_BOOLEAN),
            'is_made_in_gabon' => filter_var($request->input('is_made_in_gabon'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'prix_promotion' => 'nullable|integer',
            'is_promotion' => 'boolean',
            'boutique_id' => 'required|exists:boutiques,id',
            'is_made_in_gabon' => 'boolean',
            'variations' => 'required|array',
            'variations.*.couleur' => 'required|string',
            'variations.*.taille' => 'required|string',
            'variations.*.quantite' => 'required|integer',
            'images' => 'nullable|array', // Images de l'article principal
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048', // Validation d'image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Création de l'article
        $article = Article::create($request->only([
            'nom',
            'description',
            'prix',
            'prix_promotion',
            'is_promotion',
            'boutique_id',
            'categorie_id',
        ]));

        // Ajout des images de l'article principal en utilisant 'url_photo'
        if ($request->has('images')) {
            foreach ($request->images as $imageFile) {
                $path = $imageFile->store('images/articles');
                $article->images()->create(['url_photo' => $path]);
            }
        }

        // Ajout des variations
        foreach ($request->variations as $variationData) {
            $variation = $article->variations()->create([
                'couleur' => $variationData['couleur'],
                'taille' => $variationData['taille'],
            ]);

            // Ajout du stock pour la variation
            $variation->stock()->create([
                'quantite' => $variationData['quantite'],
            ]);

            // Ajout des images pour la variation en utilisant 'url_photo'
            if (isset($variationData['images'])) {
                foreach ($variationData['images'] as $imageFile) {
                    $path = $imageFile->store('images/variations');
                    $variation->images()->create(['url_photo' => $path]);
                }
            }
        }

        return response()->json($article->load('variations.stock', 'images', 'variations.images'), 201);
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
            $query->where('quantite', '>=', 1);
        })->get();

        return response()->json($articles);
    }

}