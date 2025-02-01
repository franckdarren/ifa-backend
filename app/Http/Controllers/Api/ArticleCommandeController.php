<?php

namespace App\Http\Controllers\Api;

use App\Models\Commande;
use Illuminate\Http\Request;
use App\Models\ArticleCommande;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ArticleCommandeController extends Controller
{
    public function attachArticle(Request $request, $commande_id)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
            'prix' => 'required|integer',
            'reduction' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $commande = Commande::find($commande_id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->articles()->attach($request->article_id, [
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
