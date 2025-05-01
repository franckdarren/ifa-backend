<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     title="Article",
 *     required={"id", "nom", "prix", "isPromotion", "pourcentageReduction", "madeInGabon", "user_id", "categorie"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nom", type="string", example="Chemise en lin"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Chemise légère pour l'été"),
 *     @OA\Property(property="prix", type="integer", example=10000),
 *     @OA\Property(property="prixPromotion", type="integer", nullable=true, example=8000),
 *     @OA\Property(property="isPromotion", type="boolean", example=true),
 *     @OA\Property(property="pourcentageReduction", type="integer", example=20),
 *     @OA\Property(property="madeInGabon", type="boolean", example=true),
 *     @OA\Property(property="user_id", type="integer", example=2),
 *     @OA\Property(property="categorie", type="string", example="Vêtements Homme"),
 *     @OA\Property(property="image_principale", type="string", nullable=true, example="https://cdn.maboutique.com/images/article123.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-30T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-30T12:34:56Z")
 * )
 */
class ArticleSchema
{
}