<?php

namespace App\Http\Controllers\Admin;

use App\Models\Annonce;
use App\Models\PhotoAnnonce;

class PhotoAnnonceController extends CrudController
{
    protected function model(): string
    {
        return PhotoAnnonce::class;
    }

    protected function title(): string
    {
        return 'Photo d’annonce';
    }

    protected function routeName(): string
    {
        return 'admin.photos-annonces';
    }

    protected function fields(): array
    {
        return [
            'annonce_id' => ['label' => 'Annonce', 'type' => 'select', 'options_model' => Annonce::class, 'option_label' => 'titre', 'rules' => ['required', 'exists:annonces,id']],
            'chemin' => ['label' => 'Chemin du fichier', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'nom_original' => ['label' => 'Nom original', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            'type_mime' => ['label' => 'Type MIME', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            'taille' => ['label' => 'Taille (octets)', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            'est_principale' => ['label' => 'Photo principale', 'type' => 'checkbox', 'rules' => ['nullable', 'boolean']],
            'ordre' => ['label' => 'Ordre', 'type' => 'number', 'rules' => ['required', 'integer', 'between:0,65535']],
        ];
    }
}
