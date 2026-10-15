<x-app-layout>
    <section class="conversation-page">
        <header class="conversation-page-heading">
            <div>
                <p class="eyebrow"><span></span> Vos échanges</p>
                <h1>Discussions</h1>
                <p>Échangez directement avec les membres au sujet d’une annonce.</p>
            </div>
        </header>

        @if ($conversations->isEmpty())
            <div class="conversation-empty">
                <span aria-hidden="true">◷</span>
                <h2>Pas encore de discussion</h2>
                <p>Depuis une annonce publiée, choisissez « Contacter » pour démarrer un échange.</p>
                <a class="conversation-primary-link" href="{{ url('/') }}#annonces">Parcourir les annonces <span aria-hidden="true">↗</span></a>
            </div>
        @else
            <div class="conversation-list">
                @foreach ($conversations as $conversation)
                    @php
                        $interlocuteur = (int) $conversation->initiateur_id === $userId
                            ? $conversation->destinataire
                            : $conversation->initiateur;
                        $lastMessage = $conversation->dernierMessage;
                    @endphp
                    <a class="conversation-row" href="{{ route('conversations.show', $conversation) }}">
                        <span class="conversation-avatar">{{ $interlocuteur?->initiales ?? 'M' }}</span>
                        <span class="conversation-row-main">
                            <span class="conversation-row-top"><strong>{{ $interlocuteur?->nom_complet ?? 'Membre' }}</strong><time>{{ ($lastMessage?->created_at ?? $conversation->created_at)->diffForHumans() }}</time></span>
                            <span class="conversation-row-ad">{{ $conversation->annonce?->titre ?? 'Discussion' }}</span>
                            <span class="conversation-row-preview">{{ $lastMessage?->contenu ?? 'Conversation démarrée' }}</span>
                        </span>
                        @if ($conversation->unread_messages_count)
                            <span class="conversation-unread">{{ $conversation->unread_messages_count }}</span>
                        @endif
                        <span class="conversation-row-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
            {{ $conversations->links() }}
        @endif
    </section>
</x-app-layout>