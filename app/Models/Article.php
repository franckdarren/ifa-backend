<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'prixPromotion',
        'quantité',
        'isDisponible',
        'isPromotion',
        'pourcentageReduction',
        'boutique_id', // Lien avec la boutique de l'article
        'sous_categorie_id', // Lien avec la sous-catégorie de l'article
    ];

    /**
     * Relation avec le modèle `Boutique`.
     * Un article appartient à une boutique.
     */
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    /**
     * Relation avec le modèle `SousCategorie`.
     * Un article appartient à une sous-catégorie.
     */
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorie::class);
    }

    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class, 'article_commandes')
                    ->withPivot('quantite', 'prix', 'reduction')
                    ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ImageArticle::class);
    }

}
