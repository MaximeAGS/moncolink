<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Annonce extends Model
{
    use SoftDeletes;

    protected $table = 'annonces';

    protected $fillable = [
        'utilisateur_id',
        'type',
        'titre',
        'description',
        'type_logement',
        'ville_id',
        'commune_id',
        'quartier_id',
        'loyer_total',
        'contribution_mensuelle',
        'charges_supplementaires',
        'disponible_a_partir_du',
        'capacite',
        'equipements',
        'conditions_particulieres',
        'budget_max',
        'date_emmenagement',
        'type_logement_recherche',
        'preferences_vie',
        'informations_complementaires',
        'statut',
        'nombre_consultations',
        'publiee_le',
    ];

    protected function casts(): array
    {
        return [
            'equipements' => 'array',
            'preferences_vie' => 'array',
            'disponible_a_partir_du' => 'date',
            'date_emmenagement' => 'date',
            'publiee_le' => 'datetime',
        ];
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }

    public function photos()
    {
        return $this->hasMany(PhotoAnnonce::class, 'annonce_id');
    }

    public function photoPrincipale()
    {
        return $this->hasOne(PhotoAnnonce::class, 'annonce_id')
            ->where('est_principale', true);
    }
}
