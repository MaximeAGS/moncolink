<?php

namespace App\Http\Controllers\Admin;

use App\Models\Annonce;
use App\Models\Favori;
use App\Models\Utilisateur;

class FavoriController extends CrudController
{
    protected function model(): string
    {
        return Favori::class;
    }

    protected function title(): string
    {
        return 'Favori';
    }

    protected function routeName(): string
    {
        return 'admin.favoris';
    }

    protected function fields(): array
    {
        return [
            'utilisateur_id' => ['label' => 'Utilisateur', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'exists:utilisateurs,id']],
            'annonce_id' => ['label' => 'Annonce', 'type' => 'select', 'options_model' => Annonce::class, 'option_label' => 'titre', 'rules' => ['required', 'exists:annonces,id']],
        ];
    }
}
