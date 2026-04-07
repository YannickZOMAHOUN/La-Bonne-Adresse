<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    protected $fillable = [
        'etablissement_id',
        'url',
        'type',
        'ordre',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * URL publique du fichier
     */
    public function getUrlPubliqueAttribute(): string
    {
        return Storage::disk('public')->url($this->url);
    }

    /**
     * Vrai si le fichier est un PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->type === 'pdf';
    }
}
