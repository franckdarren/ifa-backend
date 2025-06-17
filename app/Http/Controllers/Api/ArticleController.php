<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\ImageArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="Liste des articles",
     *     tags={"Articles"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des articles récupérée avec succès"
     *     )
     * )
     */
    public function index()
    {
        $articles = Article::with(['variations'])->get();
        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**

 * @OA\Post(
 *     path="/api/articles",
 *     summary="Créer un nouvel article",
 *     tags={"Articles"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"nom", "prix", "user_id", "categorie", "variations"},
 *                 @OA\Property(property="nom", type="string", example="T-shirt Oversize"),
 *                 @OA\Property(property="description", type="string", example="Un t-shirt oversize 100% coton, très confortable."),
 *                 @OA\Property(property="prix", type="integer", example=12000),
 *                 @OA\Property(property="prixPromotion", type="integer", example=10000),
 *                 @OA\Property(property="isPromotion", type="boolean", example=true),
 *                 @OA\Property(property="pourcentageReduction", type="integer", example=20),
 *                 @OA\Property(property="madeInGabon", type="boolean", example=false),
 *                 @OA\Property(property="user_id", type="integer", example=3),
 *                 @OA\Property(property="categorie", type="string", example="Vêtements Homme"),
 *                 @OA\Property(property="image_principale", type="file"),
 *                 @OA\Property(
 *                     property="variations",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="taille", type="string", example="M"),
 *                         @OA\Property(property="couleur", type="string", example="Noir"),
 *                         @OA\Property(property="stock", type="integer", example=10),
 *                         @OA\Property(property="prix", type="number", format="float", example=12000),
 *                         @OA\Property(property="image", type="file")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Article créé avec succès"
 *     )
 * )
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
            'user_id' => 'required|exists:users,id',
            'categorie' => 'required|string|max:255',
            // 'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_principale' => 'required|string',


            'variations' => 'required|array|min:1',
            'variations.*.taille' => 'required|string',
            'variations.*.couleur' => 'required|string',
            'variations.*.stock' => 'required|integer|min:0',
            'variations.*.prix' => 'required|numeric',
            // 'variations.*.image' => 'nullable|image|max:2048',
            'variations.*.image' => 'nullable|string',

        ]);

        // if ($request->hasFile('image_principale')) {
        //     $path = $request->file('image_principale')->store('articles', 'public');
        //     $validatedData['image_principale'] = $path;
        // }

        $article = Article::create(Arr::except($validatedData, ['variations']));


        foreach ($validatedData['variations'] as $variationData) {
            // $imagePath = null;
            // if (isset($variationData['image'])) {
            //     $imagePath = $variationData['image']->store('variations', 'public');
            // }

            $article->variations()->create([
                'taille' => $variationData['taille'],
                'couleur' => $variationData['couleur'],
                'stock' => $variationData['stock'],
                'prix' => $variationData['prix'],
                'image' => $variationData['image'],


            ]);
        }

        return response()->json($article->load('variations'), 201);
    }


    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Afficher un article spécifique",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article trouvé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
     */

    public function show(string $id)
    {
        $article = Article::with(['variations'])->find($id);

        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        return response()->json($article, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     summary="Mettre à jour un article",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/ArticleStoreRequest"),
     *     @OA\Response(
     *         response=200,
     *         description="Article mis à jour avec succès"
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|integer|min:0',
            'prixPromotion' => 'nullable|integer|min:0',
            'isPromotion' => 'boolean',
            'pourcentageReduction' => 'nullable|integer|min:0|max:100',
            'madeInGabon' => 'boolean',
            'user_id' => 'required|exists:users,id', // ✅ correction ici
            'categorie' => 'required|string|max:255',
            'image_principale' => 'nullable|string', // ✅ on reste sur string comme dans store()

            'variations' => 'nullable|array|min:1',
            'variations.*.taille' => 'required|string',
            'variations.*.couleur' => 'required|string',
            'variations.*.stock' => 'required|integer|min:0',
            'variations.*.prix' => 'required|numeric',
            'variations.*.image' => 'nullable|string',
        ]);

        // Mise à jour de l'article sans les variations
        $article->update(Arr::except($validatedData, ['variations']));

        // Si les variations sont fournies, on les remplace toutes
        if (isset($validatedData['variations'])) {
            $article->variations()->delete();

            foreach ($validatedData['variations'] as $variationData) {
                $article->variations()->create([
                    'taille' => $variationData['taille'],
                    'couleur' => $variationData['couleur'],
                    'stock' => $variationData['stock'],
                    'prix' => $variationData['prix'],
                    'image' => $variationData['image'] ?? null,
                ]);
            }
        }

        return response()->json($article->load('variations'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     summary="Supprimer un article",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article non trouvé"
     *     )
     * )
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
    /**
     * @OA\Get(
     *     path="/api/articles/boutique/{id}",
     *     summary="Lister les articles d'une boutique",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la boutique",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Articles de la boutique récupérés"
     *     )
     * )
     */

    public function articlesBoutique(string $id)
    {
        return response()->json(
            Article::where('user_id', $id)
                ->with(['variations'])
                ->get(),
            200
        );
    }

    // Récupérer les articles disponibles

    // public function articlesDisponibles(string $id)
    // {
    //     $articles = Article::whereHas('variations.stocks', function ($query) {
    //         $query->where('stock', '>=', 1);
    //     })->get();

    //     return response()->json($articles);
    // }

    // filtrer les articles par catégorie
    /**
     * @OA\Get(
     *     path="/api/articles/categorie/{categorie}",
     *     summary="Lister les articles par catégorie",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="categorie",
     *         in="path",
     *         required=true,
     *         description="Nom de la catégorie",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Articles de la catégorie récupérés"
     *     )
     * )
     */
    public function articlesCategorie(string $categorie)
    {
        $articles = Article::where('categorie', $categorie)
            ->with(['variations'])
            ->get();

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'Aucun article trouvé pour cette catégorie'], 404);
        }

        return response()->json($articles, 200);
    }

    // Récupérer les articles en promotion
    /**
     * @OA\Get(
     *     path="/api/articles/promotion",
     *     summary="Lister les articles en promotion",
     *     tags={"Articles"},
     *     @OA\Response(
     *         response=200,
     *         description="Articles en promotion récupérés"
     *     )
     * )
     */
    public function articlesPromotion()
    {
        $articles = Article::where('isPromotion', true)
            ->with(['variations'])
            ->get();

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'Aucun article en promotion trouvé'], 404);
        }

        return response()->json($articles, 200);
    }

    // Rechercher un article
    /**
     * @OA\Get(
     *     path="/api/articles/recherche/{query}",
     *     summary="Rechercher des articles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="query",
     *         in="path",
     *         required=true,
     *         description="Termes de recherche",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Articles trouvés"
     *     )
     * )
     */
    public function searchArticles(string $query)
    {
        $articles = Article::where('nom', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->with(['variations'])
            ->get();

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'Aucun article trouvé pour cette recherche'], 404);
        }

        return response()->json($articles, 200);
    }

    // Lister les nouveaux articles
    /**
     * @OA\Get(
     *     path="/api/articles/nouveaux",
     *     summary="Lister les nouveaux articles",
     *     tags={"Articles"},
     *     @OA\Response(
     *         response=200,
     *         description="Nouveaux articles récupérés"
     *     )
     * )
     */
    public function nouveauxArticles()
    {
        $articles = Article::with(['variations'])
            ->orderBy('created_at', 'desc')
            ->take(10) // Limite à 10 articles les plus récents
            ->get();

        if ($articles->isEmpty()) {
            return response()->json(['message' => 'Aucun nouvel article trouvé'], 404);
        }

        return response()->json($articles, 200);
    }
}