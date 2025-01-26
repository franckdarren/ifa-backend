<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $fillable = [
        'adresse',
        'details',
        'statut',
        'state_livraison',
        'ville',
        'phone',
        'commande_id',
        'user_id', // Facultatif selon que la livraison est attribuée à un utilisateur
    ];

    /**
     * Relation avec le modèle `Commande`.
     * Une livraison appartient à une commande.
     */
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Relation avec le modèle `User`.
     * Une livraison appartient à un utilisateur (facultatif).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
