<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

class UserController extends CrudController
{
    protected function model(): string
    {
        return User::class;
    }

    protected function title(): string
    {
        return 'Compte système';
    }

    protected function routeName(): string
    {
        return 'admin.users';
    }

    protected function fields(): array
    {
        return [
            'name' => ['label' => 'Nom', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            'email' => ['label' => 'Adresse e-mail', 'type' => 'email', 'rules' => ['required', 'email', 'max:255'], 'unique' => 'email'],
            'password' => ['label' => 'Mot de passe', 'type' => 'password', 'create_rules' => ['required', 'string', 'min:8'], 'update_rules' => ['nullable', 'string', 'min:8']],
        ];
    }
}
