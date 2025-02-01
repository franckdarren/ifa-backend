<?php

namespace App\Http\Controllers\Api;

use App\Models\Boutique;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $validator = Validator::make($request->all(), [
            'adresse' => 'required|string',
            'nom' => 'required|string',
            'phone' => 'required|string',
            'url_logo' => 'required|string',
            'heure_ouverture' => 'required',
            'heure_fermeture' => 'required',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $boutique = Boutique::create($request->all());
        return response()->json($boutique, 201);
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
            'url_logo' => 'required|string',
            'heure_ouverture' => 'required',
            'heure_fermeture' => 'required',
            'description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $boutique->update($request->all());
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
