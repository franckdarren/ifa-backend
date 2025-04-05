<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['variation_id', 'quantite'];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}