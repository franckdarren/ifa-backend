<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Reclamation",
 *     type="object",
 *     required={"description", "phone", "statut", "commande_id", "user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="description", type="string", example="Produit non conforme à la commande."),
 *     @OA\Property(property="phone", type="string", example="061234567"),
 *     @OA\Property(
 *         property="statut",
 *         type="string",
 *         enum={"En attente de traitement", "En cours", "Rejetée", "Remboursée"},
 *         example="En attente de traitement"
 *     ),
 *     @OA\Property(property="commande_id", type="integer", example=12),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-27T08:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-06-27T08:45:00Z")
 * )
 */

class ReclamationSchema
{
}