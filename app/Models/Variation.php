<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    protected $fillable = ['article_id', 'couleur', 'taille'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function images()
    {
        return $this->hasMany(ImageArticle::class);
    }
}