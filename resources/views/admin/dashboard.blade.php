<x-app-layout>
    <div class="admin-dashboard">
        <header class="admin-dashboard-heading">
            <div>
                <p class="admin-eyebrow">Pilotage {{ $branding?->nom ?? config('app.name') }} <span>{{ now()->format('d.m.Y') }}</span></p>
                <h1>Vue d’ensemble</h1>
                <p>Les chiffres clés et les éléments qui demandent votre attention.</p>
            </div>
            <a class="admin-primary-action" href="{{ route('admin.annonces.index') }}">Gérer les annonces <span aria-hidden="true">↗</span></a>
        </header>

        <section class="admin-metrics" aria-label="Indicateurs clés">
            <article class="admin-metric admin-metric-accent">
                <span class="admin-metric-label">Annonces publiées</span>
                <strong>{{ $annonceCount }}</strong>
                <span class="admin-metric-note">{{ $pendingAnnonceCount }} en attente de traitement</span>
            </article>
            <article class="admin-metric">
                <span class="admin-metric-label">Membres</span>
                <strong>{{ $userCount }}</strong>
                <span class="admin-metric-note">{{ $activeUserCount }} comptes actifs</span>
            </article>
            <article class="admin-metric">
                <span class="admin-metric-label">Conversations</span>
                <strong>{{ $conversationCount }}</strong>
                <span class="admin-metric-note">Échanges enregistrés</span>
            </article>
            <article class="admin-metric {{ $reportCount ? 'admin-metric-alert' : '' }}">
                <span class="admin-metric-label">À modérer</span>
                <strong>{{ $reportCount }}</strong>
                <span class="admin-metric-note">Signalements ouverts</span>
            </article>
        </section>

        <section class="admin-dashboard-grid">
            <div class="admin-panel">
                <div class="admin-panel-heading">
                    <div><span class="admin-section-kicker">Dernières entrées</span><h2>Annonces récentes</h2></div>
                    <a href="{{ route('admin.annonces.index') }}">Tout voir <span aria-hidden="true">↗</span></a>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Annonce</th><th>Auteur</th><th>Lieu</th><th>Statut</th></tr></thead>
                        <tbody>
                            @forelse ($latestAnnonces as $annonce)
                                <tr>
                                    <td><a href="{{ route('admin.annonces.show', $annonce) }}">{{ $annonce->titre }}</a><small>{{ ucfirst($annonce->type) }}</small></td>
                                    <td>{{ $annonce->utilisateur?->nom_complet ?? '—' }}</td>
                                    <td>{{ $annonce->ville?->nom ?? 'Non précisé' }}</td>
                                    <td><span class="admin-status admin-status-{{ $annonce->statut }}">{{ str_replace('_', ' ', $annonce->statut) }}</span></td>
                                </tr>
                            @empty
                                <tr><td class="admin-empty" colspan="4">Aucune annonce enregistrée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="admin-panel admin-report-panel">
                <div class="admin-panel-heading">
                    <div><span class="admin-section-kicker">Modération</span><h2>Signalements</h2></div>
                    <a href="{{ route('admin.signalements.index') }}">Voir tout <span aria-hidden="true">↗</span></a>
                </div>
                <div class="admin-report-list">
                    @forelse ($latestSignalements as $signalement)
                        <a class="admin-report-row" href="{{ route('admin.signalements.show', $signalement) }}">
                            <span class="admin-report-dot"></span>
                            <span><strong>{{ str_replace('_', ' ', $signalement->motif) }}</strong><small>{{ $signalement->auteur?->nom_complet ?? 'Membre' }} · {{ $signalement->created_at->diffForHumans() }}</small></span>
                            <span class="admin-report-arrow" aria-hidden="true">↗</span>
                        </a>
                    @empty
                        <p class="admin-empty-message">Aucun signalement pour le moment.</p>
                    @endforelse
                </div>
                <a class="admin-panel-bottom-link" href="{{ route('admin.signalements.index') }}">Ouvrir la file de modération <span aria-hidden="true">→</span></a>
            </aside>
        </section>
    </div>
</x-app-layout>