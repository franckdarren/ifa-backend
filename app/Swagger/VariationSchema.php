<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Variation",
 *     type="object",
 *     title="Variation",
 *     required={"id", "article_id", "stock"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="article_id", type="integer", example=12, description="ID de l'article parent"),
 *     @OA\Property(property="couleur", type="string", nullable=true, example="Rouge"),
 *     @OA\Property(property="taille", type="string", nullable=true, example="M"),
 *     @OA\Property(property="stock", type="integer", example=10),
 *     @OA\Property(property="prix", type="integer", nullable=true, example=15000),
 *     @OA\Property(property="image", type="string", nullable=true, example="https://cdn.maboutique.com/variations/img1.jpg"),
 *
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-30T08:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-30T10:00:00Z")
 * )
 */
class VariationSchema
{
}