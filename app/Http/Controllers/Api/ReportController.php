<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dashboardStats(Request $request)
    {
        // Récupère l'utilisateur connecté
        $user = $request->user();

        // Récupère la boutique de l'utilisateur
        $boutique = $user->boutique;

        // Récupère le nombre d'articles, de ventes et d'alertes
        return response()->json([
            'articles_count' => $boutique->articles()->count(),  // Nombre d'articles
            'ventes_count' => $boutique->ventes()->count(),      // Nombre de ventes
            'alertes_count' => $boutique->alertes()->count(),    // Nombre d'alertes (ajouté ici)
        ]);
    }


}