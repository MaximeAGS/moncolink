<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quartier extends Model
{
    protected $fillable = ['commune_id', 'nom'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
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
