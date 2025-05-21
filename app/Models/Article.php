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
        'user_id',
        'categorie',
        'madeInGabon',
        'image_principale',

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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commandes()
    {
        return $this->belongsToMany(Commande::class, 'commande_articles')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }


    public function images()
    {
        return $this->hasMany(ImageArticle::class)->whereNull('variation_id'); // Récupère uniquement les images générales;
    }

}