<x-app-layout>
    @php
        $displayName = $user->nom_complet ?? $user->name ?? $user->email;
        $firstName = $user->prenom ?? explode(' ', $displayName)[0];
    @endphp

    <div class="admin-dashboard member-dashboard">
        @if (session('status'))
            <div class="admin-settings-success" role="status">{{ session('status') }}</div>
        @endif

        <header class="admin-dashboard-heading">
            <div>
                <p class="admin-eyebrow">Espace membre {{ $branding?->nom ?? config('app.name') }} <span>{{ now()->format('d.m.Y') }}</span></p>
                <h1>Bonjour, <em>{{ $firstName }}</em></h1>
                <p>Vos annonces, vos échanges et votre profil réunis au même endroit.</p>
            </div>
            <div class="member-dashboard-actions">
                <a class="member-outline-action" href="{{ route('conversations.index') }}">Discussions @if ($unreadMessageCount)<span class="member-unread-count">{{ $unreadMessageCount }}</span>@endif</a>
                <a class="admin-primary-action" href="{{ route('mes-annonces.create') }}">Publier une annonce <span aria-hidden="true">＋</span></a>
            </div>
        </header>

        <section class="admin-metrics member-metrics" aria-label="Résumé de votre activité">
            <article class="admin-metric admin-metric-accent">
                <span class="admin-metric-label">Mes annonces</span>
                <strong>{{ $annonceCount }}</strong>
                <span class="admin-metric-note">Toutes vos publications</span>
            </article>
            <article class="admin-metric">
                <span class="admin-metric-label">Publiées</span>
                <strong>{{ $publishedCount }}</strong>
                <span class="admin-metric-note">Visibles sur le site</span>
            </article>
            <article class="admin-metric">
                <span class="admin-metric-label">Discussions</span>
                <strong>{{ $conversationCount }}</strong>
                <span class="admin-metric-note">Échanges avec les membres</span>
            </article>
            <article class="admin-metric {{ $unreadMessageCount ? 'admin-metric-alert' : '' }}">
                <span class="admin-metric-label">Messages non lus</span>
                <strong>{{ $unreadMessageCount }}</strong>
                <span class="admin-metric-note">À consulter</span>
            </article>
            <article class="admin-metric">
                <span class="admin-metric-label">Favoris</span>
                <strong>{{ $favoriteCount }}</strong>
                <span class="admin-metric-note">Annonces sauvegardées</span>
            </article>
        </section>

        <section class="member-dashboard-grid">
            <div class="admin-panel" id="mes-annonces">
                <div class="admin-panel-heading">
                    <div><span class="admin-section-kicker">Votre activité</span><h2>Mes annonces</h2></div>
                    <a href="{{ route('mes-annonces.create') }}">Nouvelle annonce <span aria-hidden="true">＋</span></a>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table member-listing-table">
                        <thead><tr><th>Annonce</th><th>Lieu</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse ($annonces as $annonce)
                                <tr>
                                    <td><strong>{{ $annonce->titre }}</strong><small>{{ $annonce->type === 'offre' ? 'Chambre disponible' : 'Recherche de colocation' }}</small></td>
                                    <td>{{ collect([$annonce->quartier?->nom, $annonce->commune?->nom, $annonce->ville?->nom])->filter()->join(', ') ?: 'Lieu à préciser' }}</td>
                                    <td><span class="admin-status admin-status-{{ $annonce->statut }}">{{ str_replace('_', ' ', $annonce->statut) }}</span></td>
                                    <td>
                                        <div class="member-listing-actions">
                                            <a href="{{ route('mes-annonces.edit', $annonce) }}">Modifier</a>
                                            <form method="POST" action="{{ route('mes-annonces.destroy', $annonce) }}" onsubmit="return confirm('Supprimer cette annonce ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="admin-empty" colspan="4">Vous n’avez pas encore publié d’annonce. <a href="{{ route('mes-annonces.create') }}">Créer ma première annonce</a></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="member-side-column">
                <section class="admin-panel member-conversations-panel">
                    <div class="admin-panel-heading">
                        <div><span class="admin-section-kicker">Vos échanges</span><h2>Discussions récentes</h2></div>
                        <a href="{{ route('conversations.index') }}">Tout voir <span aria-hidden="true">↗</span></a>
                    </div>
                    <div class="member-conversation-list">
                        @forelse ($conversations as $conversation)
                            @php
                                $interlocuteur = (int) $conversation->initiateur_id === (int) $user->getKey()
                                    ? $conversation->destinataire
                                    : $conversation->initiateur;
                            @endphp
                            <a class="member-conversation-row" href="{{ route('conversations.show', $conversation) }}">
                                <span class="conversation-avatar">{{ $interlocuteur?->initiales ?? 'M' }}</span>
                                <span><strong>{{ $interlocuteur?->nom_complet ?? 'Membre' }}</strong><small>{{ $conversation->dernierMessage?->contenu ?? $conversation->annonce?->titre ?? 'Conversation démarrée' }}</small></span>
                                <span class="member-conversation-arrow" aria-hidden="true">↗</span>
                            </a>
                        @empty
                            <p class="member-no-conversations">Aucune discussion pour le moment.</p>
                        @endforelse
                    </div>
                </section>

                <section class="member-profile-card">
                    <div class="profile-panel-top">
                        <span class="profile-initials">{{ $user->initiales ?? mb_strtoupper(mb_substr($displayName, 0, 1)) }}</span>
                        <span class="profile-status"><i></i> Compte actif</span>
                    </div>
                    <p class="admin-section-kicker">Votre profil</p>
                    <h2>{{ $displayName }}</h2>
                    <p>{{ $user->profession ?? 'Membre '.($branding?->nom ?? config('app.name')) }}@if ($user->ville) <span>·</span> {{ $user->ville->nom }}@endif</p>
                    <a class="member-profile-link" href="{{ route('profile.edit') }}">Modifier mon profil <span aria-hidden="true">↗</span></a>
                </section>
            </aside>
        </section>

        <footer class="dashboard-footer"><x-brand-logo :href="route('dashboard')" /><span>{{ $branding?->slogan ?: 'Un chez-vous, à plusieurs.' }}</span></footer>
    </div>
</x-app-layout>
