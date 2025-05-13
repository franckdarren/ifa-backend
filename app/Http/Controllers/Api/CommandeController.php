<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class CommandeController extends Controller
{
    /**
     * Créer une commande.
     */
    /**
     * @OA\Post(
     *     path="/api/commandes",
     *     summary="Créer une commande",
     *     tags={"Commandes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "isLivrable", "articles"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="commentaire", type="string", example="Livraison rapide svp"),
     *             @OA\Property(property="isLivrable", type="boolean", example=true),
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"article_id", "quantite"},
     *                     @OA\Property(property="article_id", type="integer", example=10),
     *                     @OA\Property(property="variation_id", type="integer", nullable=true, example=4),
     *                     @OA\Property(property="quantite", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Commande créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=42),
     *             @OA\Property(property="numero", type="string", example="CMD-20250513-XYZ"),
     *             @OA\Property(property="statut", type="string", example="En attente"),
     *             @OA\Property(property="prix", type="integer", example=15000),
     *             @OA\Property(property="isLivrable", type="boolean", example=true),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="article_id", type="integer", example=10),
     *                     @OA\Property(property="variation_id", type="integer", nullable=true, example=4),
     *                     @OA\Property(property="quantite", type="integer", example=2),
     *                     @OA\Property(property="prix_unitaire", type="integer", example=5000)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'commentaire' => 'nullable|string',
            'isLivrable' => 'required|boolean',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.variation_id' => 'nullable|exists:variations,id',
            'articles.*.quantite' => 'required|integer|min:1',
        ]);


        DB::beginTransaction();

        try {
            $total = 0;

            // Générer un numéro unique de commande
            $numeroCommande = 'CMD-' . strtoupper(uniqid());

            // Créer la commande
            $commande = Commande::create([
                'numero' => $numeroCommande,
                'user_id' => $validated['user_id'],
                'commentaire' => $validated['commentaire'] ?? '',
                'statut' => 'En attente',
                'isLivrable' => $validated['isLivrable'],
                'prix' => 0, // temporairement, sera mis à jour après
            ]);

            foreach ($validated['articles'] as $item) {
                $article = Article::findOrFail($item['article_id']);

                if (!empty($item['variation_id'])) {
                    $variation = $article->variations()->findOrFail($item['variation_id']);

                    // Si le prix de la variation est différent de 0, on l'utilise, sinon on prend le prix de l'article
                    $prixUnitaire = $variation->prix != 0 ? $variation->prix : ($article->isPromotion ? $article->prixPromotion : $article->prix);
                } else {
                    // Si aucune variation n'est spécifiée, on prend directement le prix de l'article
                    $prixUnitaire = $article->isPromotion ? $article->prixPromotion : $article->prix;
                }


                $sousTotal = $prixUnitaire * $item['quantite'];
                $total += $sousTotal;

                $commande->articles()->attach($article->id, [
                    'variation_id' => $item['variation_id'] ?? null,
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $prixUnitaire,
                ]);
            }


            // Mise à jour du prix total
            $commande->update(['prix' => $total]);

            DB::commit();

            return response()->json([
                'message' => 'Commande créée avec succès',
                'commande' => $commande->load('articles'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Liste des commandes (facultatif)
     */
    /**
     * @OA\Get(
     *     path="/api/commandes",
     *     summary="Lister toutes les commandes",
     *     tags={"Commandes"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des commandes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="numero", type="string", example="CMD-20250513-XYZ"),
     *                 @OA\Property(property="statut", type="string", example="En attente"),
     *                 @OA\Property(property="prix", type="integer", example=15000),
     *                 @OA\Property(property="commentaire", type="string", example="Livraison rapide svp"),
     *                 @OA\Property(property="isLivrable", type="boolean", example=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-13T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-13T12:10:00Z"),
     *                 @OA\Property(
     *                     property="articles",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="nom", type="string", example="T-shirt Gabon"),
     *                         @OA\Property(
     *                             property="pivot",
     *                             type="object",
     *                             @OA\Property(property="quantite", type="integer", example=2),
     *                             @OA\Property(property="prix_unitaire", type="integer", example=5000),
     *                             @OA\Property(property="variation_id", type="integer", nullable=true, example=4)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $commandes = Commande::with('articles')->latest()->get();
        return response()->json($commandes);
    }

    /**
     * Détails d'une commande
     */
    /**
     * @OA\Get(
     *     path="/api/commandes/{id}",
     *     summary="Afficher une commande spécifique",
     *     tags={"Commandes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la commande",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la commande",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="numero", type="string", example="CMD-20250513-XYZ"),
     *             @OA\Property(property="statut", type="string", example="En attente"),
     *             @OA\Property(property="prix", type="integer", example=15000),
     *             @OA\Property(property="commentaire", type="string", example="Livraison rapide svp"),
     *             @OA\Property(property="isLivrable", type="boolean", example=true),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-13T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-13T12:10:00Z"),
     *             @OA\Property(
     *                 property="articles",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="nom", type="string", example="T-shirt Gabon"),
     *                     @OA\Property(
     *                         property="pivot",
     *                         type="object",
     *                         @OA\Property(property="quantite", type="integer", example=2),
     *                         @OA\Property(property="prix_unitaire", type="integer", example=5000),
     *                         @OA\Property(property="variation_id", type="integer", nullable=true, example=4)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Commande non trouvée"
     *     )
     * )
     */

    public function show($id)
    {
        $commande = Commande::with('articles')->findOrFail($id);
        return response()->json($commande);
    }


    /**
     * @OA\Patch(
     *     path="/api/commandes/{id}/statut",
     *     summary="Mettre à jour le statut d'une commande",
     *     tags={"Commandes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la commande",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"statut"},
     *             @OA\Property(
     *                 property="statut",
     *                 type="string",
     *                 example="Livrée",
     *                 description="Nouveau statut de la commande. Valeurs possibles : En attente, En préparation, Prête pour livraison, En cours de livraison, Livrée, Annulée, Remboursée"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statut mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Statut mis à jour avec succès."),
     *             @OA\Property(
     *                 property="commande",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="statut", type="string", example="Livrée"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-13T14:30:00Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-13T13:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation échouée"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Commande non trouvée"
     *     )
     * )
     */

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'statut' => [
                'required',
                Rule::in([
                    'En attente',
                    'En préparation',
                    'Prête pour livraison',
                    'En cours de livraison',
                    'Livrée',
                    'Annulée',
                    'Remboursée'
                ]),
            ],
        ]);

        $commande = Commande::findOrFail($id);
        $commande->statut = $validated['statut'];
        $commande->save();

        return response()->json([
            'message' => 'Statut mis à jour avec succès.',
            'commande' => $commande
        ]);
    }


}
