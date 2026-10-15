<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signalement extends Model
{
    protected $table = 'signalements';

    protected $fillable = [
        'signale_par',
        'signalable_id',
        'signalable_type',
        'motif',
        'details',
        'statut',
        'traite_par',
        'traite_le',
    ];

    protected function casts(): array
    {
        return [
            'traite_le' => 'datetime',
        ];
    }

    public function signalable()
    {
        return $this->morphTo();
    }

    public function auteur()
    {
        return $this->belongsTo(Utilisateur::class, 'signale_par');
    }

    public function moderateur()
    {
        return $this->belongsTo(Utilisateur::class, 'traite_par');
    }
}