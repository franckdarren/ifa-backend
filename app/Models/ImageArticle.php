<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageArticle extends Model
{
    protected $fillable = [
        'url_photo',
        'couleur',
        'article_id', // Lien avec l'article
    ];

    /**
     * Relation avec le modèle `Article`.
     * Une image appartient à un article.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
