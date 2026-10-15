<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoAnnonce extends Model
{
    protected $table = 'photo_annonces';

    protected $fillable = [
        'annonce_id',
        'chemin',
        'nom_original',
        'type_mime',
        'taille',
        'est_principale',
        'ordre',
    ];

    protected function casts(): array
    {
        return [
            'est_principale' => 'boolean',
        ];
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class, 'annonce_id');
    }
}