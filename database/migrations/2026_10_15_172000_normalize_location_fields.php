<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ville_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nom');
            $table->timestamps();
            $table->unique(['ville_id', 'nom']);
        });

        Schema::create('quartiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nom');
            $table->timestamps();
            $table->unique(['commune_id', 'nom']);
        });

        foreach (['utilisateurs', 'annonces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('ville_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('commune_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('quartier_id')->nullable()->constrained()->nullOnDelete();
            });
        }

        $this->convertExistingLocations('utilisateurs');
        $this->convertExistingLocations('annonces');

        foreach (['utilisateurs', 'annonces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['ville', 'commune', 'quartier']);
            });
        }
    }

    public function down(): void
    {
        foreach (['utilisateurs', 'annonces'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('ville')->nullable();
                $table->string('commune')->nullable();
                $table->string('quartier')->nullable();
            });

            foreach (DB::table($tableName)->get(['id', 'ville_id', 'commune_id', 'quartier_id']) as $record) {
                DB::table($tableName)->where('id', $record->id)->update([
                    'ville' => $record->ville_id ? DB::table('villes')->where('id', $record->ville_id)->value('nom') : null,
                    'commune' => $record->commune_id ? DB::table('communes')->where('id', $record->commune_id)->value('nom') : null,
                    'quartier' => $record->quartier_id ? DB::table('quartiers')->where('id', $record->quartier_id)->value('nom') : null,
                ]);
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('quartier_id');
                $table->dropConstrainedForeignId('commune_id');
                $table->dropConstrainedForeignId('ville_id');
            });
        }

        Schema::dropIfExists('quartiers');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('villes');
    }

    private function convertExistingLocations(string $tableName): void
    {
        foreach (DB::table($tableName)->get(['id', 'ville', 'commune', 'quartier']) as $record) {
            $villeName = trim((string) $record->ville);
            $communeName = trim((string) $record->commune);
            $quartierName = trim((string) $record->quartier);

            $villeId = $villeName === '' ? null : $this->findOrCreateLocation('villes', $villeName);
            $communeId = $communeName === ''
                ? null
                : $this->findOrCreateLocation('communes', $communeName, 'ville_id', $villeId);
            $quartierId = $quartierName === ''
                ? null
                : $this->findOrCreateLocation('quartiers', $quartierName, 'commune_id', $communeId);

            DB::table($tableName)->where('id', $record->id)->update([
                'ville_id' => $villeId,
                'commune_id' => $communeId,
                'quartier_id' => $quartierId,
            ]);
        }
    }

    private function findOrCreateLocation(string $tableName, string $name, ?string $parentColumn = null, ?int $parentId = null): int
    {
        $query = DB::table($tableName)->where('nom', $name);
        if ($parentColumn !== null) {
            $parentId === null
                ? $query->whereNull($parentColumn)
                : $query->where($parentColumn, $parentId);
        }

        $id = $query->value('id');
        if ($id !== null) {
            return (int) $id;
        }

        $now = now();
        $data = ['nom' => $name, 'created_at' => $now, 'updated_at' => $now];
        if ($parentColumn !== null) {
            $data[$parentColumn] = $parentId;
        }

        return (int) DB::table($tableName)->insertGetId($data);
    }
};
