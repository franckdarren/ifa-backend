<?php

namespace App\Swagger;

/**
 * @OA\RequestBody(
 *     request="ArticleStoreRequest",
 *     required=true,
 *     description="Données pour créer ou mettre à jour un article",
 *     @OA\JsonContent(
 *         required={"nom", "prix", "categorie", "user_id"},
 *         @OA\Property(property="nom", type="string", example="T-shirt en coton"),
 *         @OA\Property(property="description", type="string", example="T-shirt 100% coton, confortable"),
 *         @OA\Property(property="prix", type="integer", example=10000),
 *         @OA\Property(property="prixPromotion", type="integer", nullable=true, example=8000),
 *         @OA\Property(property="isPromotion", type="boolean", example=false),
 *         @OA\Property(property="pourcentageReduction", type="integer", example=0),
 *         @OA\Property(property="madeInGabon", type="boolean", example=false),
 *         @OA\Property(property="user_id", type="integer", example=1),
 *         @OA\Property(property="categorie", type="string", example="Vêtements Femme"),
 *         @OA\Property(property="image_principale", type="string", nullable=true, example="https://cdn.maboutique.com/images/tshirt123.jpg")
 *     )
 * )
 */
class ArticleRequest
{
}
