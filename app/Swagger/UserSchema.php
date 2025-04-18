<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"id", "name", "role", "email"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Boutique LeBoss"),
 *     @OA\Property(property="role", type="string", example="Boutique"),
 *     @OA\Property(property="email", type="string", format="email", example="leboss@boutique.com"),

 *     @OA\Property(property="url_logo", type="string", nullable=true, example="https://cdn.maboutique.com/logo123.png"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+241074000000"),
 *     @OA\Property(property="heure_ouverture", type="string", nullable=true, example="08:00"),
 *     @OA\Property(property="heure_fermeture", type="string", nullable=true, example="18:00"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Boutique spécialisée dans les vêtements africains"),
 *     @OA\Property(property="solde", type="integer", example=15000),

 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example="2025-04-18T10:30:00Z"),
 *     @OA\Property(property="password", type="string", nullable=true, example=null),
 *     @OA\Property(property="remember_token", type="string", nullable=true, example="token123"),
 *     @OA\Property(property="current_team_id", type="integer", nullable=true, example=2),
 *     @OA\Property(property="profile_photo_path", type="string", nullable=true, example="/storage/profile/user123.jpg"),
 *     @OA\Property(property="firebase_uid", type="string", nullable=true, example="firebase-uid-abc123"),
 *     @OA\Property(property="abonnement", type="string", example="Simple"),

 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-18T08:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-04-18T10:00:00Z")
 * )
 */

class UserSchema
{
}
