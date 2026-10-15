<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlocageUtilisateur;
use App\Models\Utilisateur;

class BlocageUtilisateurController extends CrudController
{
    protected function model(): string
    {
        return BlocageUtilisateur::class;
    }

    protected function title(): string
    {
        return 'Blocage utilisateur';
    }

    protected function routeName(): string
    {
        return 'admin.blocages';
    }

    protected function fields(): array
    {
        return [
            'bloque_par' => ['label' => 'Bloqué par', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'exists:utilisateurs,id']],
            'utilisateur_bloque' => ['label' => 'Utilisateur bloqué', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'different:bloque_par', 'exists:utilisateurs,id']],
        ];
    }
}
