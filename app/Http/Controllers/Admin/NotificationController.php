<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;

class NotificationController extends CrudController
{
    protected function model(): string
    {
        return Notification::class;
    }

    protected function title(): string
    {
        return 'Notification';
    }

    protected function routeName(): string
    {
        return 'admin.notifications';
    }

    protected function fields(): array
    {
        return [
            'type' => ['label' => 'Type', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'notifiable_type' => ['label' => 'Type de destinataire', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'notifiable_id' => ['label' => 'ID du destinataire', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1']],
            'data' => ['label' => 'Données (JSON)', 'type' => 'json', 'rules' => ['required', 'json']],
            'read_at' => ['label' => 'Lue le', 'type' => 'datetime-local', 'rules' => ['nullable', 'date']],
        ];
    }
}
