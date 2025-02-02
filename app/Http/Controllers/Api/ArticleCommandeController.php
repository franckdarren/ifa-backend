<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Commande;
use Illuminate\Http\Request;
use App\Models\ArticleCommande;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ArticleCommandeController extends Controller
{
    public function attachArticle(Request $request, $commande_id)
{
    // Validation des données
    $validator = Validator::make($request->all(), [
        'article_id' => 'required|exists:articles,id',
        'quantite' => 'required|integer|min:1',
        'prix' => 'required|numeric',
        'reduction' => 'nullable|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Récupération de la commande
    $commande = Commande::find($commande_id);
    if (!$commande) {
        return response()->json(['message' => 'Commande non trouvée'], 404);
    }

    // Vérification de l'existence de l'article
    $article = Article::find($request->article_id);
    if (!$article) {
        return response()->json(['message' => 'Article non trouvé'], 404);
    }

    // Attachement de l'article à la commande avec les détails
    $commande->articles()->attach($article->id, [
        'quantite' => $request->quantite,
        'prix' => $request->prix,
        'reduction' => $request->reduction ?? 0,
    ]);

    return response()->json(['message' => 'Article ajouté à la commande avec succès'], 201);
}

    public function detachArticle($commande_id, $article_id)
    {
        $commande = Commande::find($commande_id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->articles()->detach($article_id);

        return response()->json(['message' => 'Article retiré de la commande'], 200);
    }
}
