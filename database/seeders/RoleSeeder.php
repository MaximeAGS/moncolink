<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        $administrateur = Role::firstOrCreate([
            'name' => 'administrateur',
            'guard_name' => $guard,
        ]);

        Role::firstOrCreate([
            'name' => 'utilisateur',
            'guard_name' => $guard,
        ]);

        // Création des permissions
        $permissions = [
            'gerer utilisateurs',
            'gerer annonces',
            'moderer signalements',
            'gerer parametres',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        // Attribution des permissions à l'administrateur
        $administrateur->syncPermissions($permissions);

        // Réinitialisation du cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
