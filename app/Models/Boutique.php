<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boutique extends Model
{
    protected $fillable = [
        'adresse',
        'nom',
        'phone',
        'url_logo',
        'heure_ouverture',
        'heure_fermeture',
        'description',
        'user_id', // Lien avec l'utilisateur propriétaire de la boutique
    ];

    /**
     * Relation avec le modèle `User`.
     * Une boutique appartient à un utilisateur (propriétaire).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
