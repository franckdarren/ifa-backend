<?php

namespace App\Http\Controllers\Api;

use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BoutiqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Boutique::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Validation des données
        $validator = Validator::make($request->all(), [
            'adresse' => 'required|string',
            'nom' => 'required|string',
            'phone' => 'required|string',
            'heure_ouverture' => 'required',
            'heure_fermeture' => 'required',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'url_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Correction ici
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageUrl = null;

        // ✅ Vérifier et stocker l'image correctement
        if ($request->hasFile('url_logo')) { // 🔥 Correction ici
            $file = $request->file('url_logo');

            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stocker dans DigitalOcean Spaces
                $imagePath = $file->store('boutiques', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stocker localement dans storage/app/public/boutiques
                $imagePath = $file->store('boutiques', 'public');
                $imageUrl = Storage::url($imagePath);
            }
        }

        // ✅ Création de la boutique avec l'image
        $boutique = Boutique::create([
            'adresse' => $request->adresse,
            'nom' => $request->nom,
            'phone' => $request->phone,
            'heure_ouverture' => $request->heure_ouverture,
            'heure_fermeture' => $request->heure_fermeture,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'url_logo' => $imageUrl, // 🔥 Correction ici
        ]);

        return response()->json([
            'message' => 'Boutique créée avec succès !',
            'boutique' => $boutique
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $boutique = Boutique::find($id);
        if (!$boutique) {
            return response()->json(['message' => 'Boutique non trouvée'], 404);
        }
        return response()->json($boutique, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $boutique = Boutique::find($id);
        if (!$boutique) {
            return response()->json(['message' => 'Boutique non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'adresse' => 'required|string',
            'nom' => 'required|string',
            'phone' => 'required|string',
            'heure_ouverture' => 'required',
            'heure_fermeture' => 'required',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'url_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Gestion de l'upload d'une nouvelle image
        if ($request->hasFile('image')) {
            if (env('USE_DIGITALOCEAN_SPACES', false)) {
                // 🔥 Stocker dans DigitalOcean Spaces
                $imagePath = $request->file('image')->store('boutiques', 'spaces');
                $imageUrl = Storage::disk('spaces')->url($imagePath);
            } else {
                // 📁 Stocker dans storage/app/public/boutiques
                $imagePath = $file->store('boutiques', 'public');
                $imageUrl = Storage::url($imagePath);
            }

            // 🗑️ Supprimer l'ancienne image (facultatif)
            if ($boutique->url_logo) {
                Storage::delete($boutique->image_url);
            }

            $boutique->url_logo = $imageUrl;
        }

        $boutique->update($request->except('image'));
        return response()->json($boutique, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $boutique = Boutique::find($id);
        if (!$boutique) {
            return response()->json(['message' => 'Boutique non trouvée'], 404);
        }

        $boutique->delete();
        return response()->json(['message' => 'Boutique supprimée avec succès'], 200);
    }
}
