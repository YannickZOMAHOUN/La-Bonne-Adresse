<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = ['nom', 'slug', 'emoji', 'description', 'active'];

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }
}
