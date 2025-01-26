<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicite extends Model
{
    protected $fillable = [
        'date_start',
        'date_end',
        'titre',
        'url_image',
        'lien',
        'description',
        'isActif',
    ];

    // Les colonnes à caster dans un type spécifique
    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'isActif' => 'boolean',
    ];

    /**
     * Scope pour récupérer uniquement les publicités actives.
     */
    public function scopeActives($query)
    {
        return $query->where('isActif', true)
            ->whereDate('date_start', '<=', now())
            ->whereDate('date_end', '>=', now());
    }

    /**
     * Vérifie si la publicité est actuellement active.
     */
    public function estActive()
    {
        return $this->isActif && $this->date_start <= now() && $this->date_end >= now();
    }
}

