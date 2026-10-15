<?php

namespace App\Http\Controllers\Admin;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Utilisateur;

class MessageController extends CrudController
{
    protected function model(): string
    {
        return Message::class;
    }

    protected function title(): string
    {
        return 'Message';
    }

    protected function routeName(): string
    {
        return 'admin.messages';
    }

    protected function fields(): array
    {
        return [
            'conversation_id' => ['label' => 'Conversation', 'type' => 'select', 'options_model' => Conversation::class, 'option_label' => 'id', 'rules' => ['required', 'exists:conversations,id']],
            'expediteur_id' => ['label' => 'Expéditeur', 'type' => 'select', 'options_model' => Utilisateur::class, 'option_label' => 'email', 'rules' => ['required', 'exists:utilisateurs,id']],
            'contenu' => ['label' => 'Contenu', 'type' => 'textarea', 'rules' => ['required', 'string']],
            'lu_le' => ['label' => 'Lu le', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
        ];
    }
}
