<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function dashboardStats($userId)
    {
        // Récupère l'utilisateur par son ID
        $user = User::with('boutique')->find($userId);

        if (!$user) {
            return response()->json([
                'error' => 'Utilisateur non trouvé.'
            ], 404);
        }

        $boutique = $user->boutique;

        if (!$boutique) {
            return response()->json([
                'error' => 'Aucune boutique associée à cet utilisateur.'
            ], 404);
        }

        return response()->json([
            'articles_count' => $boutique->articles()->count(),
            // 'ventes_count' => $boutique->ventes()->count(),
            // 'alertes_count' => $boutique->alertes()->count(),
        ]);
    }




}