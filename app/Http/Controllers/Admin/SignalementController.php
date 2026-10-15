<?php

namespace App\Http\Controllers\Admin;

use App\Models\Signalement;
use App\Models\Utilisateur;

class SignalementController extends CrudController
{
    protected function model(): string
    {
        return Signalement::class;
    }

    protected function title(): string
    {
        return 'Signalement';
    }

    protected function routeName(): string
    {
        return 'admin.signalements';
    }

    protected function fields(): array
    {
        return [
            'signale_par' => ['label' => 'Auteur', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'exists:utilisateurs,id']],
            'signalable_type' => ['label' => 'Type de contenu signalé', 'type' => 'select', 'options' => ['App\\Models\\Annonce' => 'Annonce', 'App\\Models\\Utilisateur' => 'Utilisateur'], 'rules' => ['required', 'in:App\\Models\\Annonce,App\\Models\\Utilisateur']],
            'signalable_id' => ['label' => 'ID du contenu signalé', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1']],
            'motif' => ['label' => 'Motif', 'type' => 'select', 'options' => ['suspecte' => 'Suspecte', 'trompeuse' => 'Trompeuse', 'photos_inappropriees' => 'Photos inappropriées', 'comportement_abusif' => 'Comportement abusif', 'spam' => 'Spam', 'autre' => 'Autre'], 'rules' => ['required', 'in:suspecte,trompeuse,photos_inappropriees,comportement_abusif,spam,autre']],
            'details' => ['label' => 'Détails', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
            'statut' => ['label' => 'Statut', 'type' => 'select', 'options' => ['en_attente' => 'En attente', 'en_examen' => 'En examen', 'resolu' => 'Résolu', 'rejete' => 'Rejeté'], 'rules' => ['required', 'in:en_attente,en_examen,resolu,rejete']],
            'traite_par' => ['label' => 'Modérateur', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['nullable', 'exists:utilisateurs,id']],
            'traite_le' => ['label' => 'Traité le', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
        ];
    }
}
