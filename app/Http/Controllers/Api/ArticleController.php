<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Article::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'prixPromotion' => 'nullable|integer',
            'type' => 'required|string',
            'caracteristiques' => 'required|array',
            'boutique_id' => 'required|exists:boutiques,id',
            'sous_categorie_id' => 'required|exists:sous_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Calculer le pourcentage de réduction si prixPromotion est présent
        if (!empty($data['prixPromotion']) && $data['prix'] > 0) {
            $data['pourcentageReduction'] = (1 - ($data['prixPromotion'] / $data['prix'])) * 100;
        } else {
            $data['pourcentageReduction'] = 0; // Pas de réduction
        }

        $article = Article::create($data);

        return response()->json($article, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::with('images')->find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }
        return response()->json($article, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'description' => 'required|string',
            'prix' => 'required|integer',
            'prixPromotion' => 'required|integer',

            'type' => 'required|integer',
            'carateristique' => 'required|integer',

            'isDisponible' => 'required|boolean',
            'isPromotion' => 'required|boolean',
            'pourcentageReduction' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $article->update($request->all());
        return response()->json($article, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        $article->delete();
        return response()->json(['message' => 'Article supprimé avec succès'], 200);
    }

    // Récupérer le schéma du formulaire
    public function getSchema(Request $request)
    {
        $type = $request->query('type'); // Récupère le paramètre "type"

        $schemas = config('formSchemas'); // Charge le fichier config/formSchemas.php
        $schema = $schemas[$type] ?? []; // Utilise la clé correspondant au type ou un tableau vide si introuvable

        return response()->json(['fields' => $schema]);
    }


    // Récupérer les articles d'une boutique
    public function articlesBoutique(string $id)
    {
        return response()->json(
            Article::where('boutique_id', $id)->get(),
            200
        );
    }

    // Récupérer les articles disponibles
    public function articlesDisponibles(string $id)
    {
        $articles = Article::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(caracteristiques, '$.quantite')) >= 1")->get();

        return response()->json($articles);
    }
}