<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCommande extends Model
{
    // Nom de la table associée
    protected $table = 'article_commandes';

    // Attributs pouvant être assignés en masse
    protected $fillable = ['article_id', 'commande_id', 'quantite', 'prix', 'reduction'];

    // Cast des attributs
    protected $casts = [
        'quantite' => 'integer',
        'prix' => 'integer',
        'reduction' => 'integer',
    ];

    // Relation avec le modèle Article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // Relation avec le modèle Commande
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
