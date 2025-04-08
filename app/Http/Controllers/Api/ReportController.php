<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dashboardStats(Request $request)
    {
        $user = $request->user();
        $boutique = $user->boutique;

        return response()->json([
            'articles_count' => $boutique->articles()->count(),
            'ventes_count' => $boutique->ventes()->count(),
        ]);
    }

}