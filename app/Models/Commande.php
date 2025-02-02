<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Commande extends Model
{
    protected $fillable = [
        'numero',
        'statut',
        'prix',
        'commentaire',
        'isLivrable',
        'user_id', // Lien avec l'utilisateur qui a passé la commande
    ];

    // Les colonnes à caster dans un type spécifique
    protected $casts = [
        'isLivrable' => 'boolean',
    ];

    /**
     * Relation avec le modèle `User`.
     * Une commande appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_commandes')
                    ->withPivot('quantite', 'prix', 'reduction')
                    ->withTimestamps();
    }

    public function livraison()
    {
        return $this->hasOne(Livraison::class);
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class);
    }
}
