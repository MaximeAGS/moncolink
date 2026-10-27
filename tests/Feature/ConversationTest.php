<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_can_start_a_conversation_and_reuse_it_from_an_ad(): void
    {
        $author = $this->createUtilisateur('author@example.com');
        $visitor = $this->createUtilisateur('visitor@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)
            ->post(route('annonces.conversations.store', $annonce))
            ->assertRedirect();

        $conversation = Conversation::sole();
        $this->assertSame($visitor->id, $conversation->initiateur_id);
        $this->assertSame($author->id, $conversation->destinataire_id);

        $this->post(route('annonces.conversations.store', $annonce))
            ->assertRedirect(route('conversations.show', $conversation));

        $this->assertDatabaseCount('conversations', 1);
    }

    public function test_home_shows_contact_action_to_other_members_and_marks_owners_own_listing(): void
    {
        $author = $this->createUtilisateur('home-author@example.com');
        $visitor = $this->createUtilisateur('home-visitor@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)
            ->get('/')
            ->assertOk()
            ->assertSee('Contacter l’auteur');

        $this->actingAs($author)
            ->get('/')
            ->assertOk()
            ->assertSee('Votre annonce')
            ->assertDontSee('Contacter l’auteur');
    }

    public function test_participants_can_exchange_messages_and_opening_marks_them_read(): void
    {
        $author = $this->createUtilisateur('message-author@example.com');
        $visitor = $this->createUtilisateur('message-visitor@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)->post(route('annonces.conversations.store', $annonce));
        $conversation = Conversation::sole();

        $this->post(route('conversations.messages.store', $conversation), [
            'contenu' => 'Bonjour, votre annonce est-elle toujours disponible ?',
        ])->assertRedirect(route('conversations.show', $conversation).'#messages');

        $message = Message::sole();
        $this->assertSame($visitor->id, $message->expediteur_id);
        $this->assertNotNull($conversation->fresh()->dernier_message_le);

        $this->actingAs($author)
            ->get(route('conversations.index'))
            ->assertOk()
            ->assertSee('Test Membre')
            ->assertSee('Chambre disponible')
            ->assertSee('1');

        $this->actingAs($author)
            ->get(route('conversations.show', $conversation))
            ->assertOk()
            ->assertSee('Bonjour, votre annonce est-elle toujours disponible ?');

        $this->assertNotNull($message->fresh()->lu_le);
    }

    public function test_participants_can_send_and_view_an_image_attachment(): void
    {
        Storage::fake('local');
        $author = $this->createUtilisateur('attachment-author@example.com');
        $visitor = $this->createUtilisateur('attachment-visitor@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)->post(route('annonces.conversations.store', $annonce));
        $conversation = Conversation::sole();

        $this->post(route('conversations.messages.store', $conversation), [
            'piece_jointe' => UploadedFile::fake()->image('chambre.png'),
        ])->assertRedirect(route('conversations.show', $conversation).'#messages');

        $message = Message::sole();
        $this->assertNull($message->contenu);
        $this->assertSame('chambre.png', $message->fichier_nom);
        Storage::disk('local')->assertExists($message->fichier_chemin);

        $attachmentUrl = route('conversations.messages.attachment', [$conversation, $message]);
        $this->get($attachmentUrl)
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->actingAs($author)
            ->get(route('conversations.show', $conversation))
            ->assertOk()
            ->assertSee('chambre.png')
            ->assertSee($attachmentUrl);

        $stranger = $this->createUtilisateur('attachment-stranger@example.com');
        $this->actingAs($stranger)
            ->get($attachmentUrl)
            ->assertNotFound();
    }

    public function test_attachment_uploads_require_a_message_or_a_supported_file(): void
    {
        $author = $this->createUtilisateur('invalid-attachment-author@example.com');
        $visitor = $this->createUtilisateur('invalid-attachment-visitor@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)->post(route('annonces.conversations.store', $annonce));
        $conversation = Conversation::sole();

        $this->post(route('conversations.messages.store', $conversation), [])
            ->assertSessionHasErrors(['contenu', 'piece_jointe']);

        $this->post(route('conversations.messages.store', $conversation), [
            'piece_jointe' => UploadedFile::fake()->create('script.exe', 10),
        ])->assertSessionHasErrors('piece_jointe');

        $this->assertDatabaseCount('messages', 0);

        $this->post(route('conversations.messages.store', $conversation), [
            'piece_jointe' => UploadedFile::fake()->create('reglement.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('conversations.show', $conversation).'#messages');

        $message = Message::sole();
        $this->assertSame('reglement.pdf', $message->fichier_nom);
        $this->get(route('conversations.messages.attachment', [$conversation, $message]))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=reglement.pdf');
    }

    public function test_non_participants_cannot_read_or_send_messages(): void
    {
        $author = $this->createUtilisateur('private-author@example.com');
        $visitor = $this->createUtilisateur('private-visitor@example.com');
        $stranger = $this->createUtilisateur('stranger@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)->post(route('annonces.conversations.store', $annonce));
        $conversation = Conversation::sole();

        $this->actingAs($stranger)
            ->get(route('conversations.show', $conversation))
            ->assertNotFound();

        $this->post(route('conversations.messages.store', $conversation), [
            'contenu' => 'Message interdit',
        ])->assertNotFound();

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_discussion_has_block_and_unblock_actions_that_disable_and_restore_messaging(): void
    {
        $author = $this->createUtilisateur('block-action-author@example.com');
        $visitor = $this->createUtilisateur('block-action-visitor@example.com');
        $annonce = $this->createPublishedAnnonce($author);

        $this->actingAs($visitor)->post(route('annonces.conversations.store', $annonce));
        $conversation = Conversation::sole();

        $this->get(route('conversations.show', $conversation))
            ->assertOk()
            ->assertSee('Bloquer');

        $this->post(route('utilisateurs.blocages.store', $author))
            ->assertRedirect();

        $this->get(route('conversations.show', $conversation))
            ->assertOk()
            ->assertSee('Débloquer')
            ->assertSee('Vous avez bloqué ce membre')
            ->assertDontSee('Écrivez votre message');

        $this->post(route('conversations.messages.store', $conversation), [
            'contenu' => 'Ce message doit être refusé.',
        ])->assertForbidden();

        $this->delete(route('utilisateurs.blocages.destroy', $author))
            ->assertRedirect();

        $this->get(route('conversations.show', $conversation))
            ->assertOk()
            ->assertSee('Bloquer')
            ->assertSee('Écrivez votre message');
    }

    public function test_members_cannot_start_a_conversation_with_themselves_or_an_unpublished_ad(): void
    {
        $owner = $this->createUtilisateur('self-contact@example.com');
        $annonce = $this->createPublishedAnnonce($owner);

        $this->actingAs($owner)
            ->post(route('annonces.conversations.store', $annonce))
            ->assertForbidden();

        $unpublishedAnnonce = $this->createPublishedAnnonce($this->createUtilisateur('pending-author@example.com'));
        $unpublishedAnnonce->update(['statut' => 'en_attente']);

        $this->post(route('annonces.conversations.store', $unpublishedAnnonce))
            ->assertNotFound();
    }

    public function test_guests_are_sent_to_login_before_opening_their_inbox(): void
    {
        $this->get(route('conversations.index'))
            ->assertRedirect(route('login'));
    }

    private function createUtilisateur(string $email): Utilisateur
    {
        return Utilisateur::create([
            'prenom' => 'Test',
            'nom' => 'Membre',
            'email' => $email,
            'mot_de_passe' => 'Password@123',
        ]);
    }

    private function createPublishedAnnonce(Utilisateur $author): Annonce
    {
        return Annonce::create([
            'utilisateur_id' => $author->id,
            'type' => 'offre',
            'titre' => 'Chambre disponible',
            'description' => 'Une chambre à partager.',
            'statut' => 'publiee',
        ]);
    }
}
