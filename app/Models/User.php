<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom', 'email', 'telephone', 'password', 'role', 'statut'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Rôles ─────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProprietaire(): bool
    {
        return $this->role === 'proprietaire';
    }

    public function isActif(): bool
    {
        return $this->statut === 'actif';
    }

    // ── Relations ─────────────────────────────────────────
    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }
}
