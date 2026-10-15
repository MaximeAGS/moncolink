<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\BlocageUtilisateur;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Utilisateur;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $this->memberId($request);
        $conversations = $this->participantConversations($userId)
            ->with(['annonce', 'initiateur', 'destinataire', 'dernierMessage.expediteur'])
            ->withCount(['messages as unread_messages_count' => fn ($query) => $query
                ->whereNull('lu_le')
                ->where('expediteur_id', '!=', $userId)])
            ->orderByDesc('dernier_message_le')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('conversations.index', compact('conversations', 'userId'));
    }

    public function startFromAnnonce(Request $request, Annonce $annonce): RedirectResponse
    {
        $userId = $this->memberId($request);

        abort_unless($annonce->statut === 'publiee', 404);
        abort_if((int) $annonce->utilisateur_id === $userId, 403);
        abort_if(BlocageUtilisateur::conflit($userId, (int) $annonce->utilisateur_id), 403);

        $conversation = Conversation::query()
            ->where('annonce_id', $annonce->id)
            ->where(function ($query) use ($userId, $annonce) {
                $query->where(function ($pair) use ($userId, $annonce) {
                    $pair->where('initiateur_id', $userId)
                        ->where('destinataire_id', $annonce->utilisateur_id);
                })->orWhere(function ($pair) use ($userId, $annonce) {
                    $pair->where('initiateur_id', $annonce->utilisateur_id)
                        ->where('destinataire_id', $userId);
                });
            })
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'annonce_id' => $annonce->id,
                'initiateur_id' => $userId,
                'destinataire_id' => $annonce->utilisateur_id,
            ]);
        }

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $userId = $this->memberId($request);
        $conversation = $this->participantConversations($userId)
            ->with(['annonce', 'initiateur', 'destinataire', 'messages.expediteur'])
            ->findOrFail($conversation->id);

        $conversation->messages()
            ->where('expediteur_id', '!=', $userId)
            ->whereNull('lu_le')
            ->update(['lu_le' => now()]);
        $conversation->load('messages.expediteur');

        $interlocuteur = (int) $conversation->initiateur_id === $userId
            ? $conversation->destinataire
            : $conversation->initiateur;
        $hasBlockedInterlocuteur = BlocageUtilisateur::existe($userId, (int) $interlocuteur->id);
        $blockedByInterlocuteur = BlocageUtilisateur::existe((int) $interlocuteur->id, $userId);

        return view('conversations.show', compact(
            'conversation',
            'interlocuteur',
            'userId',
            'hasBlockedInterlocuteur',
            'blockedByInterlocuteur',
        ));
    }

    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $userId = $this->memberId($request);
        $conversation = $this->participantConversations($userId)->findOrFail($conversation->id);
        $otherUserId = (int) ($conversation->initiateur_id === $userId
            ? $conversation->destinataire_id
            : $conversation->initiateur_id);
        abort_if(BlocageUtilisateur::conflit($userId, $otherUserId), 403);

        $validated = $request->validate([
            'contenu' => ['required', 'string', 'max:5000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'expediteur_id' => $userId,
            'contenu' => trim($validated['contenu']),
        ]);

        $conversation->update(['dernier_message_le' => now()]);

        return redirect()->route('conversations.show', $conversation)->withFragment('messages');
    }

    private function memberId(Request $request): int
    {
        abort_unless($request->user() instanceof Utilisateur, 403);

        return (int) $request->user()->getKey();
    }

    private function participantConversations(int $userId)
    {
        return Conversation::query()->where(function ($query) use ($userId) {
            $query->where('initiateur_id', $userId)
                ->orWhere('destinataire_id', $userId);
        });
    }
}
