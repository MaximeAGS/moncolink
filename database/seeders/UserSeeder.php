<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Quartier;
use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ville = Ville::firstOrCreate(['nom' => 'Cotonou']);
        $communeCotonou = Commune::firstOrCreate([
            'ville_id' => $ville->id,
            'nom' => 'Cotonou',
        ]);
        $communeAbomeyCalavi = Commune::firstOrCreate([
            'ville_id' => $ville->id,
            'nom' => 'Abomey-Calavi',
        ]);
        $quartierZogbadje = Quartier::firstOrCreate([
            'commune_id' => $communeAbomeyCalavi->id,
            'nom' => 'Zogbadjè',
        ]);

        // Création de l'administrateur
        $administrateur = Utilisateur::firstOrCreate(
            ['email' => 'admin@colocation.com'],
            [
                'prenom' => 'Admin',
                'nom' => 'Principal',
                'telephone' => '97000001',
                'mot_de_passe' => 'Admin@123456',
                'ville_id' => $ville->id,
                'commune_id' => $communeCotonou->id,
                'statut' => 'actif',
                'est_etudiant' => false,
                'visibilite_telephone' => 'prive',
            ]
        );

        $roleAdmin = Role::findByName('administrateur', 'web');
        $administrateur->assignRole($roleAdmin);

        // Création d'un utilisateur classique
        $utilisateur = Utilisateur::firstOrCreate(
            ['email' => 'utilisateur@colocation.com'],
            [
                'prenom' => 'Jean',
                'nom' => 'Dupont',
                'telephone' => '97000002',
                'mot_de_passe' => 'User@123456',
                'ville_id' => $ville->id,
                'commune_id' => $communeAbomeyCalavi->id,
                'quartier_id' => $quartierZogbadje->id,
                'profession' => 'Étudiant',
                'est_etudiant' => true,
                'visibilite_telephone' => 'prive',
                'statut' => 'actif',
            ]
        );

        $utilisateur2 = Utilisateur::firstOrCreate(
            ['email' => 'marie@colocation.com'],
            [
                'prenom' => 'Marie',
                'nom' => 'Adjovi',
                'telephone' => '97000003',
                'mot_de_passe' => 'User@123456',
                'ville_id' => $ville->id,
                'commune_id' => $communeAbomeyCalavi->id,
                'quartier_id' => $quartierZogbadje->id,
                'profession' => 'Étudiante',
                'est_etudiant' => true,
                'visibilite_telephone' => 'prive',
                'statut' => 'actif',
            ]
        );

        $roleUtilisateur = Role::findByName('utilisateur', 'web');
        $utilisateur->assignRole($roleUtilisateur);
        $utilisateur2->assignRole($roleUtilisateur);
    }
}
