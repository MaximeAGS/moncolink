<?php

namespace App\Http\Controllers\Admin;

use App\Models\Commune;
use App\Models\Quartier;
use App\Models\Utilisateur;
use App\Models\Ville;

class UtilisateurController extends CrudController
{
    protected function model(): string
    {
        return Utilisateur::class;
    }

    protected function title(): string
    {
        return 'Utilisateur';
    }

    protected function routeName(): string
    {
        return 'admin.utilisateurs';
    }

    protected function fields(): array
    {
        return [
            'prenom' => ['label' => 'Prénom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'nom' => ['label' => 'Nom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'email' => ['label' => 'Adresse e-mail', 'type' => 'email', 'rules' => ['required', 'email', 'max:255'], 'unique' => 'email'],
            'telephone' => ['label' => 'Téléphone', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255'], 'unique' => 'telephone'],
            'mot_de_passe' => ['label' => 'Mot de passe', 'type' => 'password', 'create_rules' => ['required', 'string', 'min:8'], 'update_rules' => ['nullable', 'string', 'min:8']],
            'photo_profil' => ['label' => 'Photo de profil (chemin)', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            'biographie' => ['label' => 'Biographie', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
            'ville_id' => ['label' => 'Ville', 'type' => 'select', 'options_model' => Ville::class, 'option_label' => 'nom', 'rules' => ['nullable', 'exists:villes,id']],
            'commune_id' => ['label' => 'Commune', 'type' => 'select', 'options_model' => Commune::class, 'option_label' => 'nom', 'parent_field' => 'ville_id', 'parent_column' => 'ville_id', 'rules' => ['nullable', 'exists:communes,id']],
            'quartier_id' => ['label' => 'Quartier', 'type' => 'select', 'options_model' => Quartier::class, 'option_label' => 'nom', 'parent_field' => 'commune_id', 'parent_column' => 'commune_id', 'rules' => ['nullable', 'exists:quartiers,id']],
            'profession' => ['label' => 'Profession', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            'est_etudiant' => ['label' => 'Étudiant', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
            'visibilite_telephone' => ['label' => 'Visibilité du téléphone', 'type' => 'select', 'options' => ['prive' => 'Privé', 'public' => 'Public'], 'rules' => ['required', 'in:prive,public']],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['actif' => 'Actif', 'suspendu' => 'Suspendu', 'desactive' => 'Désactivé'], 'rules' => ['required', 'in:actif,suspendu,desactive']],
        ];
    }
}
