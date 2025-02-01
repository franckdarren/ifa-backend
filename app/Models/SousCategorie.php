<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SousCategorie extends Model
{
    protected $fillable = [
        'nom',
        'categorie_id',
        'url_image',
    ];

    /**
     * Relation avec le modèle `Categorie`.
     * Une sous-catégorie appartient à une catégorie.
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
