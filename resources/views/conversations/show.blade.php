<x-app-layout>
    <section class="conversation-page conversation-thread-page">
        <a class="conversation-back-link" href="{{ route('conversations.index') }}">← Toutes les discussions</a>

        <div class="conversation-thread">
            <header class="conversation-thread-header">
                <span class="conversation-avatar">{{ $interlocuteur->initiales }}</span>
                <div>
                    <h1>{{ $interlocuteur->nom_complet }}</h1>
                    <p>{{ $conversation->annonce?->titre ?? 'Discussion' }}</p>
                </div>
                <div class="conversation-thread-actions">
                    @if ($hasBlockedInterlocuteur)
                        <form method="POST" action="{{ route('utilisateurs.blocages.destroy', $interlocuteur) }}">
                            @csrf
                            @method('DELETE')
                            <button class="conversation-block-button is-unblock" type="submit">Débloquer</button>
                        </form>
                    @elseif (! $blockedByInterlocuteur)
                        <form method="POST" action="{{ route('utilisateurs.blocages.store', $interlocuteur) }}" onsubmit="return confirm('Bloquer ce membre ? Ses annonces seront masquées et la discussion désactivée.')">
                            @csrf
                            <button class="conversation-block-button" type="submit">Bloquer</button>
                        </form>
                    @else
                        <span class="conversation-blocked-badge">Bloqué par ce membre</span>
                    @endif
                </div>
            </header>

            <div class="conversation-messages" id="messages" aria-live="polite">
                @forelse ($conversation->messages as $message)
                    @php($isOwnMessage = (int) $message->expediteur_id === $userId)
                    <article class="conversation-message {{ $isOwnMessage ? 'is-own' : '' }}">
                        <div class="conversation-message-meta">
                            <strong>{{ $isOwnMessage ? 'Vous' : $interlocuteur->nom_complet }}</strong>
                            <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('d/m/Y · H:i') }}</time>
                        </div>
                        @if ($message->contenu)
                            <p>{{ $message->contenu }}</p>
                        @endif
                        @if ($message->fichier_chemin)
                            @php($attachmentUrl = route('conversations.messages.attachment', [$conversation, $message]))
                            <div class="conversation-attachment">
                                @if (str_starts_with((string) \Illuminate\Support\Facades\Storage::disk('local')->mimeType($message->fichier_chemin), 'image/'))
                                    <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener">
                                        <img src="{{ $attachmentUrl }}" alt="Image jointe : {{ $message->fichier_nom }}">
                                    </a>
                                @endif
                                <a class="conversation-attachment-link" href="{{ $attachmentUrl }}">
                                    <span aria-hidden="true">📎</span> {{ $message->fichier_nom }}
                                </a>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="conversation-first-message">
                        <span aria-hidden="true">✉</span>
                        <p>La discussion commence ici. Présentez-vous et expliquez ce qui vous intéresse dans l’annonce.</p>
                    </div>
                @endforelse
            </div>

            @if ($hasBlockedInterlocuteur || $blockedByInterlocuteur)
                <div class="conversation-blocked-notice" role="status">
                    @if ($hasBlockedInterlocuteur)
                        Vous avez bloqué ce membre. Débloquez-le pour reprendre la discussion.
                    @else
                        Ce membre vous a bloqué. L’envoi de messages est désactivé.
                    @endif
                </div>
            @else
                <form class="conversation-composer" method="POST" enctype="multipart/form-data" action="{{ route('conversations.messages.store', $conversation) }}">
                    @csrf
                    <label class="sr-only" for="contenu">Votre message</label>
                    <textarea id="contenu" name="contenu" rows="3" maxlength="5000" placeholder="Écrivez votre message…">{{ old('contenu') }}</textarea>
                    @error('contenu')<p class="conversation-error">{{ $message }}</p>@enderror
                    <label class="conversation-attachment-input" for="piece_jointe">Joindre une image ou un fichier (10 Mo max.)</label>
                    <input id="piece_jointe" name="piece_jointe" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.odt,.txt,.rtf,.xls,.xlsx,.csv,.ppt,.pptx,.zip">
                    @error('piece_jointe')<p class="conversation-error">{{ $message }}</p>@enderror
                    <div class="conversation-composer-footer">
                        <span>Restez respectueux et ne partagez pas d’informations sensibles.</span>
                        <button class="conversation-send-button" type="submit">Envoyer <span aria-hidden="true">↗</span></button>
                    </div>
                </form>
            @endif
        </div>
    </section>
</x-app-layout>