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

        'isPromotion',
        'pourcentageReduction',

        'boutique_id',
        'categorie',

        'madeInGabon',

    ];

    protected $casts = [
        'isPromotion' => 'boolean',
        'madeInGabon' => 'boolean',
    ];


    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    /**
     * Relation avec le modèle `Boutique`.
     * Un article appartient à une boutique.
     */
    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }

    public function commandes(): BelongsToMany
    {
        return $this->belongsToMany(Commande::class, 'article_commandes')
            ->withPivot('quantite', 'prix', 'reduction')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ImageArticle::class)->whereNull('variation_id'); // Récupère uniquement les images générales;
    }

}