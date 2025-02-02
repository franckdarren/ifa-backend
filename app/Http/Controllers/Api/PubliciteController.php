<?php

namespace App\Http\Controllers\Api;

use App\Models\Publicite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    // ✅ Créer une nouvelle publicité avec gestion d'image
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'titre' => 'required|string|max:255',
            'lien' => 'required|string|max:255',
            'description' => 'required|string',
            'isActif' => 'required|boolean',
            'url_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // ✅ Image optionnelle
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageUrl = null;

        // ✅ Stockage de l'image (Local ou DigitalOcean Spaces)
        if ($request->hasFile('url_image')) {
            $file = $request->file('url_image');

            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stockage sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('publicites', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $file->store('publicites', 'public');
                $imageUrl = Storage::url($imagePath);
            }
        }

        // ✅ Création de la publicité
        $publicite = Publicite::create([
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'titre' => $request->titre,
            'lien' => $request->lien,
            'description' => $request->description,
            'isActif' => $request->isActif,
            'url_image' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Publicité créée avec succès !',
            'publicite' => $publicite
        ], 201);
    }

    // ✅ Mettre à jour une publicité avec gestion de l'image
    public function update(Request $request, $id)
    {
        $publicite = Publicite::find($id);
        if (!$publicite) {
            return response()->json(['message' => 'Publicité non trouvée'], 404);
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'titre' => 'required|string|max:255',
            'lien' => 'required|string|max:255',
            'description' => 'required|string',
            'isActif' => 'required|boolean',
            'url_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // ✅ Image optionnelle
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Gestion de l'upload d'une nouvelle image
        if ($request->hasFile('url_image')) {
            $file = $request->file('url_image');
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                $file = $request->file('url_image');
                // 🔥 Stocker sur DigitalOcean Spaces
                $imagePath = $request->file('image')->store('publicites', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stockage local
                $imagePath = $file->store('publicites', 'public');
                $imageUrl = Storage::url($imagePath);
            }

            // 🗑️ Supprimer l'ancienne image
            if ($publicite->url_image) {
                Storage::delete($publicite->url_image);
            }

            $publicite->url_image = $imageUrl;
        }

        // ✅ Mise à jour des autres champs
        $publicite->update($request->except('image'));

        return response()->json([
            'message' => 'Publicité mise à jour avec succès !',
            'publicite' => $publicite
        ], 200);
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
