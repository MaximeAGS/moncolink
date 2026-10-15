<?php

namespace App\Http\Controllers\Admin;

use App\Models\Annonce;
use App\Models\Conversation;
use App\Models\Utilisateur;

class ConversationController extends CrudController
{
    protected function model(): string
    {
        return Conversation::class;
    }

    protected function title(): string
    {
        return 'Conversation';
    }

    protected function routeName(): string
    {
        return 'admin.conversations';
    }

    protected function fields(): array
    {
        return [
            'annonce_id' => ['label' => 'Annonce', 'type' => 'select', 'options_model' => Annonce::class, 'option_label' => 'titre', 'rules' => ['nullable', 'exists:annonces,id']],
            'initiateur_id' => ['label' => 'Initiateur', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'exists:utilisateurs,id']],
            'destinataire_id' => ['label' => 'Destinataire', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'different:initiateur_id', 'exists:utilisateurs,id']],
            'dernier_message_le' => ['label' => 'Dernier message le', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
        ];
    }
}
