<?php

namespace App\Http\Controllers\Admin;

use App\Models\Annonce;
use App\Models\Commune;
use App\Models\Quartier;
use App\Models\Utilisateur;
use App\Models\Ville;

class AnnonceController extends CrudController
{
    protected function model(): string
    {
        return Annonce::class;
    }

    protected function title(): string
    {
        return 'Annonce';
    }

    protected function routeName(): string
    {
        return 'admin.annonces';
    }

    protected function fields(): array
    {
        return [
            'utilisateur_id' => ['label' => 'Utilisateur', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'exists:utilisateurs,id']],
            'type' => ['label' => 'Type', 'type' => 'select', 'options' => ['offre' => 'Offre', 'recherche' => 'Recherche'], 'rules' => ['required', 'in:offre,recherche']],
            'titre' => ['label' => 'Titre', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => ['required', 'string']],
            'type_logement' => ['label' => 'Type de logement', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            'ville_id' => ['label' => 'Ville', 'type' => 'select', 'options_model' => Ville::class, 'option_label' => 'nom', 'rules' => ['nullable', 'exists:villes,id']],
            'commune_id' => ['label' => 'Commune', 'type' => 'select', 'options_model' => Commune::class, 'option_label' => 'nom', 'parent_field' => 'ville_id', 'parent_column' => 'ville_id', 'rules' => ['nullable', 'exists:communes,id']],
            'quartier_id' => ['label' => 'Quartier', 'type' => 'select', 'options_model' => Quartier::class, 'option_label' => 'nom', 'parent_field' => 'commune_id', 'parent_column' => 'commune_id', 'rules' => ['nullable', 'exists:quartiers,id']],
            'loyer_total' => ['label' => 'Loyer total', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0']],
            'contribution_mensuelle' => ['label' => 'Contribution mensuelle', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0']],
            'charges_supplementaires' => ['label' => 'Charges supplémentaires', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0']],
            'disponible_a_partir_du' => ['label' => 'Disponible à partir du', 'type' => 'date', 'rules' => ['nullable', 'date']],
            'capacite' => ['label' => 'Capacité', 'type' => 'number', 'rules' => ['nullable', 'integer', 'between:0,255']],
            'equipements' => ['label' => 'Équipements (JSON)', 'type' => 'json', 'rules' => ['nullable', 'json']],
            'conditions_particulieres' => ['label' => 'Conditions particulières', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
            'budget_max' => ['label' => 'Budget maximum', 'type' => 'number', 'rules' => ['nullable', 'numeric', 'min:0']],
            'date_emmenagement' => ['label' => 'Date d’emménagement', 'type' => 'date', 'rules' => ['nullable', 'date']],
            'type_logement_recherche' => ['label' => 'Type de logement recherché', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            'preferences_vie' => ['label' => 'Préférences de vie (JSON)', 'type' => 'json', 'rules' => ['nullable', 'json']],
            'informations_complementaires' => ['label' => 'Informations complémentaires', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['en_attente' => 'En attente', 'publiee' => 'Publiée', 'en_discussion' => 'En discussion', 'pourvue' => 'Pourvue', 'desactivee' => 'Désactivée', 'refusee' => 'Refusée'], 'rules' => ['required', 'in:en_attente,publiee,en_discussion,pourvue,desactivee,refusee']],
            'nombre_consultations' => ['label' => 'Consultations', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0']],
            'publiee_le' => ['label' => 'Date de publication', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
        ];
    }
}
