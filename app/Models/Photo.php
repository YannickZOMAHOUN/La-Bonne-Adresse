<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    protected $fillable = ['etablissement_id', 'url', 'legende', 'ordre'];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function getUrlCompleteAttribute(): string
    {
        return asset('storage/' . $this->url);
    }
}
