<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ville extends Model
{
    protected $fillable = ['nom'];

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class);
    }

    public function utilisateurs(): HasMany
    {
        return $this->hasMany(Utilisateur::class);
    }

    public function annonces(): HasMany
    {
        return $this->hasMany(Annonce::class);
    }
}
