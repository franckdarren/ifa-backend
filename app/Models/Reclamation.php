<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    protected $fillable = [
        'description',
        'phone',
        'role',
        'commande_id',
        'user_id',
    ];

    /**
     * Relation avec le modèle `Commande`.
     * Une réclamation appartient à une commande.
     */
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Relation avec le modèle `User`.
     * Une réclamation appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
