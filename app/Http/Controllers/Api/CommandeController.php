<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Article;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class CommandeController extends Controller
{
    /**
     * Créer une commande.
     *
     * Cette route permet à un utilisateur de passer une commande avec ou sans variations
     * pour les articles sélectionnés. Elle tient compte du stock, applique des frais de
     * livraison selon la localisation et met à jour les soldes des boutiques et de
     * l'administrateur selon des paliers de prix.
     *
     * @OA\Post(
     *     path="/api/commandes",
     *     summary="Créer une commande",
     *     tags={"Commandes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "isLivrable", "adresse_livraison", "articles"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="commentaire", type="string", example="Livraison rapide svp"),
     *             @OA\Property(property="isLivrable", type="boolean", example=true),
     *             @OA\Property(property="adresse_livraison", type="string", example="Libreville"),
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
     *             @OA\Property(property="message", type="string", example="Commande créée avec succès"),
     *             @OA\Property(
     *                 property="commande",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=42),
     *                 @OA\Property(property="numero", type="string", example="CMD-ABC1234567"),
     *                 @OA\Property(property="statut", type="string", example="En attente"),
     *                 @OA\Property(property="prix", type="integer", example=15000),
     *                 @OA\Property(property="isLivrable", type="boolean", example=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="commentaire", type="string", example="Livraison rapide svp"),
     *                 @OA\Property(property="adresse_livraison", type="string", example="Libreville"),
     *                 @OA\Property(
     *                     property="articles",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="pivot", type="object",
     *                             @OA\Property(property="variation_id", type="integer", nullable=true, example=4),
     *                             @OA\Property(property="quantite", type="integer", example=2),
     *                             @OA\Property(property="prix_unitaire", type="integer", example=5000)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la création de la commande"
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
            'adresse_livraison' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $adminFrais = 0;

            // Générer un numéro de commande unique
            $numeroCommande = 'CMD-' . strtoupper(uniqid());

            // Créer la commande
            $commande = Commande::create([
                'numero' => $numeroCommande,
                'user_id' => $validated['user_id'],
                'commentaire' => $validated['commentaire'] ?? '',
                'statut' => 'En attente',
                'isLivrable' => $validated['isLivrable'],
                'prix' => 0,
                'adresse_livraison' => $validated['adresse_livraison'],
            ]);

            $admin = User::admin();

            if (!$admin) {
                throw new \Exception("Aucun administrateur trouvé. Vérifiez la méthode User::admin().");
            }

            $boutiqueIds = [];

            foreach ($validated['articles'] as $item) {
                $article = Article::findOrFail($item['article_id']);
                $quantite = $item['quantite'];

                // Stocker l'ID de la boutique si pas déjà ajouté
                $boutiqueIds[] = $article->boutique_id;

                if (!empty($item['variation_id'])) {
                    $variation = $article->variations()
                        ->where('id', $item['variation_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($variation->stock < $quantite) {
                        throw new \Exception("Stock insuffisant pour la variation ID {$variation->id}");
                    }

                    $variation->decrement('stock', $quantite);
                    $prixUnitaire = $variation->prix != 0
                        ? $variation->prix
                        : ($article->isPromotion ? $article->prixPromotion : $article->prix);
                } else {
                    $prixUnitaire = $article->isPromotion ? $article->prixPromotion : $article->prix;
                }

                $sousTotal = $prixUnitaire * $quantite;
                $total += $sousTotal;

                $commande->articles()->attach($article->id, [
                    'variation_id' => $item['variation_id'] ?? null,
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                ]);

                // Définir les frais de service selon le tarif
                if ($prixUnitaire < 15000) {
                    $frais = 300 * $quantite;
                } elseif ($prixUnitaire < 50000) {
                    $frais = 500 * $quantite;
                } else {
                    $frais = 1000 * $quantite;
                }

                $benefice = $sousTotal - $frais;

                if ($article->user) {
                    $article->user->increment('solde', $benefice);
                } else {
                    throw new \Exception("L'article ID {$article->id} n'est associé à aucune boutique.");
                }

                $adminFrais += $frais;
            }

            // Ajouter les frais de livraison selon la ville
            $baseLivraison = match (strtolower($validated['adresse_livraison'])) {
                'libreville' => 2500,
                'akanda' => 2000,
                'owendo' => 3000,
                default => 3000,
            };


            // Calcul du tarif en fonction du nombre de boutiques différentes
            $nombreBoutiques = count(array_unique($boutiqueIds));
            $livraison = min($baseLivraison * $nombreBoutiques, 8000); // plafonné à 8000 FCFA

            // Ajouter la livraison au total
            $total += $livraison;

            // Mise à jour du solde admin et du prix total de la commande
            $admin->increment('solde', $adminFrais);
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

    // Récupérer les commandes d'un utilisateur spécifique
    /**
     * @OA\Get(
     *     path="/api/commandes/user/{userId}",
     *     summary="Lister les commandes d'un utilisateur",
     *     tags={"Commandes"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des commandes de l'utilisateur",
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
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-13T12:10:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé ou aucune commande"
     *     )
     * )
     */
    public function getUserCommandes($userId)
    {
        $user = User::findOrFail($userId);
        $commandes = $user->commandes()->with('articles')->get();

        if ($commandes->isEmpty()) {
            return response()->json(['message' => 'Aucune commande trouvée pour cet utilisateur.'], 404);
        }

        return response()->json($commandes);
    }

    // Récupérer les articles d'une commande d'un utilisateur spécifique
    /**
     * @OA\Get(
     *     path="/api/commandes/{commandeId}/articles",
     *     summary="Lister les articles d'une commande spécifique",
     *     tags={"Commandes"},
     *     @OA\Parameter(
     *         name="commandeId",
     *         in="path",
     *         description="ID de la commande",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des articles de la commande",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="nom", type="string", example="T-shirt Gabon"),
     *                 @OA\Property(property="quantite", type="integer", example=2),
     *                 @OA\Property(property="prix_unitaire", type="integer", example=5000),
     *                 @OA\Property(property="variation_id", type="integer", nullable=true, example=4)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Commande non trouvée"
     *     )
     * )
     */
    public function getCommandeArticles($commandeId)
    {
        $commande = Commande::with('articles')->findOrFail($commandeId);

        if ($commande->articles->isEmpty()) {
            return response()->json(['message' => 'Aucun article trouvé pour cette commande.'], 404);
        }

        return response()->json($commande->articles);
    }
    /**
     * @OA\Delete(
     *     path="/api/commandes/{id}",
     *     summary="Supprimer une commande",
     *     tags={"Commandes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la commande à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Commande supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Commande non trouvée"
     *     )
     * )
     */
    public function destroy($id)
    {
        $commande = Commande::findOrFail($id);
        $commande->delete();

        return response()->json(null, 204);
    }


}