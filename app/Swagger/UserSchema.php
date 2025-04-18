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
 *     @OA\Property(property="name", type="string", example="Jean Dupont"),
 *     @OA\Property(property="role", type="string", example="Client"),
 *     @OA\Property(property="email", type="string", format="email", example="jean@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example="2024-04-17T10:00:00Z"),
 *     @OA\Property(property="password", type="string", nullable=true, example=null),
 *     @OA\Property(property="remember_token", type="string", nullable=true, example="randomtoken123"),
 *     @OA\Property(property="current_team_id", type="integer", nullable=true, example=2),
 *     @OA\Property(property="profile_photo_path", type="string", nullable=true, example="profile/photos/user123.jpg"),
 *     @OA\Property(property="firebase_uid", type="string", nullable=true, example="firebase-uid-abc123"),
 *     @OA\Property(property="abonnement", type="string", example="Simple"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-04-17T09:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-04-17T10:00:00Z")
 * )
 */

class UserSchema
{
}