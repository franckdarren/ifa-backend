<?php

namespace App\Http\Controllers\Api;

use App\Models\Publicite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PubliciteController extends Controller
{
    /// Récupérer toutes les publicités
    public function index()
    {
        $publicites = Publicite::all();  // Récupère toutes les publicités
        return response()->json($publicites);
    }

    // Récupérer une publicité par son ID
    public function show($id)
    {
        $publicite = Publicite::find($id);

        if (!$publicite) {
            return response()->json(['message' => 'Publicité non trouvée'], 404);
        }

        return response()->json($publicite);
    }

    // Créer une nouvelle publicité
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'titre' => 'required|string|max:255',
            'url_image' => 'required|string|max:255',
            'lien' => 'required|string|max:255',
            'description' => 'required|string',
            'isActif' => 'required|boolean',
        ]);

        // Stocker l'image et récupérer le chemin
        if ($request->hasFile('image')) {
            // Stocker l'image dans storage/app/public/publicites
            $imagePath = $request->file('image')->store('public/publicites');

            // Générer l'URL accessible publiquement
            $imageUrl = Storage::url($imagePath);
        }

        // Avec DigitalOcean Space
        // if ($request->hasFile('image')) {
        //     // Stocker dans DigitalOcean Spaces
        //     $imagePath = $request->file('image')->store('publicites', 'spaces');

        //     // Générer une URL complète de l'image
        //     $imageUrl = Storage::disk('spaces')->url($imagePath);
        // }

        // Créer la catégorie
        $publicite = Publicite::create([
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'titre' => $request->titre,
            'lien' => $request->lien,
            'description' => $request->description,
            'isActif' => $request->isActif,

            'url_image' => $imageUrl ?? null,
        ]);

        // Créer la publicité
        $publicite = Publicite::create($validatedData);

        return response()->json($publicite, 201);
    }

    // Mettre à jour une publicité existante
    public function update(Request $request, $id)
    {
        $publicite = Publicite::find($id);

        if (!$publicite) {
            return response()->json(['message' => 'Publicité non trouvée'], 404);
        }

        // Validation des données
        $validatedData = $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'titre' => 'required|string|max:255',
            'url_image' => 'required|string|max:255',
            'lien' => 'required|string|max:255',
            'description' => 'required|string',
            'isActif' => 'required|boolean',
        ]);

        // Mettre à jour la publicité
        $publicite->update($validatedData);

        return response()->json($publicite);
    }

    // Supprimer une publicité
    public function destroy($id)
    {
        $publicite = Publicite::find($id);

        if (!$publicite) {
            return response()->json(['message' => 'Publicité non trouvée'], 404);
        }

        // Supprimer la publicité
        $publicite->delete();

        return response()->json(['message' => 'Publicité supprimée avec succès']);
    }
}
