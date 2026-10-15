<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlocageUtilisateur extends Model
{
    protected $table = 'blocage_utilisateurs';

    protected $fillable = [
        'bloque_par',
        'utilisateur_bloque',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /* -----------------------------------------------------------------
     | Relations
     | ----------------------------------------------------------------- */

    /**
     * Utilisateur qui a bloqué.
     */
    public function bloqueur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'bloque_par');
    }

    /**
     * Utilisateur qui est bloqué.
     */
    public function bloque(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_bloque');
    }

    /* -----------------------------------------------------------------
     | Scopes
     | ----------------------------------------------------------------- */

    /**
     * Blocages effectués par un utilisateur donné.
     */
    public function scopePar(Builder $query, int $utilisateurId): Builder
    {
        return $query->where('bloque_par', $utilisateurId);
    }

    /**
     * Blocages subis par un utilisateur donné.
     */
    public function scopeSubisPar(Builder $query, int $utilisateurId): Builder
    {
        return $query->where('utilisateur_bloque', $utilisateurId);
    }

    /**
     * Blocages impliquant un utilisateur (dans un sens ou dans l'autre).
     */
    public function scopeImpliquant(Builder $query, int $utilisateurId): Builder
    {
        return $query->where(function (Builder $q) use ($utilisateurId) {
            $q->where('bloque_par', $utilisateurId)
              ->orWhere('utilisateur_bloque', $utilisateurId);
        });
    }

    /* -----------------------------------------------------------------
     | Méthodes utilitaires
     | ----------------------------------------------------------------- */

    /**
     * Vérifie si $bloqueurId a bloqué $bloqueId.
     */
    public static function existe(int $bloqueurId, int $bloqueId): bool
    {
        return static::query()
            ->where('bloque_par', $bloqueurId)
            ->where('utilisateur_bloque', $bloqueId)
            ->exists();
    }

    /**
     * Vérifie si deux utilisateurs sont en conflit de blocage
     * (l'un a bloqué l'autre, peu importe le sens).
     */
    public static function conflit(int $utilisateurA, int $utilisateurB): bool
    {
        return static::query()
            ->where(function (Builder $q) use ($utilisateurA, $utilisateurB) {
                $q->where('bloque_par', $utilisateurA)
                  ->where('utilisateur_bloque', $utilisateurB);
            })
            ->orWhere(function (Builder $q) use ($utilisateurA, $utilisateurB) {
                $q->where('bloque_par', $utilisateurB)
                  ->where('utilisateur_bloque', $utilisateurA);
            })
            ->exists();
    }
}
