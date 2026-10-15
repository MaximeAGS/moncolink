<x-app-layout>
    <section class="conversation-page">
        <header class="conversation-page-heading">
            <div>
                <p class="eyebrow"><span></span> Confidentialité</p>
                <h1>Membres bloqués</h1>
                <p>Leurs annonces sont masquées de votre accueil. Vous pouvez les débloquer ici.</p>
            </div>
        </header>

        @if (session('status'))
            <div class="market-feedback" role="status">{{ session('status') }}</div>
        @endif

        @if ($blocages->isEmpty())
            <div class="conversation-empty">
                <span aria-hidden="true">⊘</span>
                <h2>Aucun membre bloqué</h2>
                <p>Les membres que vous bloquez depuis une annonce apparaîtront ici.</p>
                <a class="conversation-primary-link" href="{{ url('/') }}#annonces">Retour aux annonces <span aria-hidden="true">↗</span></a>
            </div>
        @else
            <div class="conversation-list">
                @foreach ($blocages as $blocage)
                    <div class="blocked-member-row">
                        <span class="conversation-avatar">{{ $blocage->bloque?->initiales ?? 'M' }}</span>
                        <div class="blocked-member-details">
                            <strong>{{ $blocage->bloque?->nom_complet ?? 'Compte supprimé' }}</strong>
                            <span>{{ collect([$blocage->bloque?->commune?->nom, $blocage->bloque?->ville?->nom])->filter()->join(', ') }}</span>
                        </div>
                        <form method="POST" action="{{ route('utilisateurs.blocages.destroy', $blocage->utilisateur_bloque) }}">
                            @csrf
                            @method('DELETE')
                            <button class="member-outline-action" type="submit">Débloquer</button>
                        </form>
                    </div>
                @endforeach
            </div>
            {{ $blocages->links() }}
        @endif
    </section>
</x-app-layout>