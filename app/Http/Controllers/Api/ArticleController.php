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
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'prix_promotion' => 'nullable|integer',
            'is_promotion' => 'boolean',
            'boutique_id' => 'required|exists:boutiques,id',
            'sous_categorie_id' => 'required|exists:sous_categories,id',
            'is_made_in_gabon' => 'boolean',
            'type' => 'required|string',
            'variations' => 'required|array',
            'variations.*.couleur' => 'required|string',
            'variations.*.code_couleur' => 'required|string',
            'variations.*.taille' => 'required|integer',
            'variations.*.quantite' => 'required|integer',
            'images' => 'nullable|array', // Si vous voulez ajouter des images pour l'article principal
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
            'sous_categorie_id',
        ]));

        // Ajout des images de l'article principal
        if ($request->has('images')) {
            foreach ($request->images as $imageFile) {
                $path = $imageFile->store('images/articles'); // Stocke les images
                $article->images()->create(['url' => $path]); // Crée une entrée pour l'image dans la base de données
            }
        }

        // Ajout des variations
        foreach ($request->variations as $variationData) {
            $variation = $article->variations()->create([
                'couleur' => $variationData['couleur'],
                'code_couleur' => $variationData['code_couleur'],
                'taille' => $variationData['taille'],
            ]);

            // Ajout du stock
            $variation->stock()->create([
                'quantite' => $variationData['quantite'],
            ]);

            // Ajout des images de la variation
            if (isset($variationData['images'])) {
                foreach ($variationData['images'] as $imageFile) {
                    $path = $imageFile->store('images/variations');
                    $variation->images()->create(['url' => $path]);
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