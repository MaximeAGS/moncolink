<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    protected $fillable = ['ville_id', 'nom'];

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function quartiers(): HasMany
    {
        return $this->hasMany(Quartier::class);
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
