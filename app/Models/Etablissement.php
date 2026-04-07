<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use 

class Etablissement extends Model
{
    protected $fillable = [
        'user_id', 'ville_id', 'categorie_id',
        'nom', 'slug', 'description', 'adresse',
        'telephone', 'whatsapp', 'email', 'site_web',
        'latitude', 'longitude', 'horaires',
        'fourchette_prix', 'photo_principale',
        'statut', 'en_vedette',
    ];

    protected $casts = [
        'horaires'   => 'array',
        'en_vedette' => 'boolean',
    ];

    // ── Auto-slug à la création ────────────────────────────
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nom) . '-' . Str::random(5);
            }
        });
    }

    // ── Scopes utiles ──────────────────────────────────────
    public function scopeActif($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeEnVedette($query)
    {
        return $query->where('en_vedette', true)->where('statut', 'actif');
    }

    // ── Relations ─────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('ordre');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    // ── Helpers ───────────────────────────────────────────
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo_principale
            ? asset('storage/' . $this->photo_principale)
            : asset('images/placeholder.jpg');
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        $numero = preg_replace('/\D/', '', $this->whatsapp);
        return "https://wa.me/{$numero}";
    }
    public function menus(): HasMany
{
    return $this->hasMany(Menu::class)->orderBy('ordre');
}
}
