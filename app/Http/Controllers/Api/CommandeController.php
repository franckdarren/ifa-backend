<?php

namespace App\Http\Controllers\Api;

use App\Models\Commande;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CommandeController extends Controller
{
    private function generateNumeroCommande()
    {
        $lastCommande = Commande::latest('id')->first(); // Récupère la dernière commande par ID
        $nextNumber = $lastCommande ? $lastCommande->id + 1 : 1; // Utilise l'ID + 1
        return 'C-' . $nextNumber;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Commande::with('articles')->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:En attente,En préparation,Prête pour livraison,En cours de livraison,Livrée,Annulée,Remboursée',
            'prix' => 'required|integer',
            'commentaire' => 'nullable|string',
            'isLivrable' => 'required|boolean',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['numero'] = $this->generateNumeroCommande();

        $commande = Commande::create($data);
        return response()->json($commande, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $commande = Commande::with('articles')->find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        return response()->json($commande, 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $commande = Commande::find($id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:En attente,En préparation,Prête pour livraison,En cours de livraison,Livrée,Annulée,Remboursée',
            'prix' => 'required|integer',
            'commentaire' => 'nullable|string',
            'isLivrable' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $commande->update($request->all());
        return response()->json($commande, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $commande = Commande::find($id);
        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->delete();
        return response()->json(['message' => 'Commande supprimée avec succès'], 200);
    }
}
