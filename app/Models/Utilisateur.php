<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Utilisateur extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'telephone',
        'mot_de_passe',
        'photo_profil',
        'biographie',
        'ville_id',
        'commune_id',
        'quartier_id',
        'profession',
        'est_etudiant',
        'visibilite_telephone',
        'statut',
        'derniere_connexion_le',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $guard_name = 'web';

    protected function casts(): array
    {
        return [
            'email_verifie_le' => 'datetime',
            'telephone_verifie_le' => 'datetime',
            'derniere_connexion_le' => 'datetime',
            'est_etudiant' => 'boolean',
            'mot_de_passe' => 'hashed',
        ];
    }

    // Laravel utilise "password" par défaut pour l'authentification.
    // On surcharge la méthode pour utiliser "mot_de_passe".
    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function getAuthPasswordName(): string
    {
        return 'mot_de_passe';
    }

    // Accesseur : nom complet
    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    // Accesseur : initiales
    public function getInitialesAttribute(): string
    {
        $p = mb_substr($this->prenom ?? '', 0, 1);
        $n = mb_substr($this->nom ?? '', 0, 1);

        return mb_strtoupper($p.$n);
    }

    // Relations
    public function annonces()
    {
        return $this->hasMany(Annonce::class, 'utilisateur_id');
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

    public function conversationsEnvoyees()
    {
        return $this->hasMany(Conversation::class, 'initiateur_id');
    }

    public function conversationsRecues()
    {
        return $this->hasMany(Conversation::class, 'destinataire_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'signale_par');
    }
}
