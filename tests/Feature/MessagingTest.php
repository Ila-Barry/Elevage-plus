<?php
// tests/Feature/MessagingTest.php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    protected User $expediteur;
    protected User $destinataire;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->expediteur = User::factory()->create([
            'status' => 'active'
        ]);
        
        $this->destinataire = User::factory()->create([
            'status' => 'active'
        ]);
    }

    /**
     * Test l'envoi d'un message
     */
    public function test_user_can_send_message()
    {
        $response = $this->actingAs($this->expediteur)
            ->postJson('/api/messaging/send', [
                'destinataire_id' => $this->destinataire->id,
                'contenu' => 'Bonjour, comment allez-vous ?'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'conversation_id',
                    'contenu',
                    'is_sent_by_me'
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'expediteur_id' => $this->expediteur->id,
            'destinataire_id' => $this->destinataire->id,
            'contenu' => 'Bonjour, comment allez-vous ?'
        ]);
    }

    /**
     * Test l'envoi d'un message à soi-même (doit échouer)
     */
    public function test_user_cannot_send_message_to_self()
    {
        $response = $this->actingAs($this->expediteur)
            ->postJson('/api/messaging/send', [
                'destinataire_id' => $this->expediteur->id,
                'contenu' => 'Message à moi-même'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['destinataire_id']);
    }

    /**
     * Test la récupération des conversations
     */
    public function test_user_can_get_conversations()
    {
        // Créer une conversation
        $conversation = Conversation::create([
            'user1_id' => $this->expediteur->id,
            'user2_id' => $this->destinataire->id,
        ]);

        // Créer quelques messages
        Message::create([
            'conversation_id' => $conversation->id,
            'expediteur_id' => $this->expediteur->id,
            'destinataire_id' => $this->destinataire->id,
            'contenu' => 'Premier message',
            'lu' => false,
        ]);

        $response = $this->actingAs($this->expediteur)
            ->getJson('/api/messaging/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'conversations' => [
                        '*' => [
                            'id',
                            'other_participant',
                            'unread_count',
                            'derniere_message'
                        ]
                    ],
                    'pagination'
                ]
            ]);
    }

    /**
     * Test le marquage des messages comme lus
     */
    public function test_user_can_mark_messages_as_read()
    {
        // Créer une conversation
        $conversation = Conversation::create([
            'user1_id' => $this->expediteur->id,
            'user2_id' => $this->destinataire->id,
        ]);

        // Créer un message non lu
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'expediteur_id' => $this->expediteur->id,
            'destinataire_id' => $this->destinataire->id,
            'contenu' => 'Message important',
            'lu' => false,
        ]);

        $response = $this->actingAs($this->destinataire)
            ->postJson("/api/messaging/conversations/{$conversation->id}/read");

        $response->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'lu' => true,
            'lu_at' => now()
        ]);
    }

    /**
     * Test l'accès à une conversation non autorisée
     */
    public function test_user_cannot_access_unauthorized_conversation()
    {
        $otherUser = User::factory()->create();
        
        // Créer une conversation entre deux autres utilisateurs
        $conversation = Conversation::create([
            'user1_id' => $otherUser->id,
            'user2_id' => $this->destinataire->id,
        ]);

        $response = $this->actingAs($this->expediteur)
            ->getJson("/api/messaging/conversations/{$conversation->id}/messages");

        $response->assertStatus(403);
    }

    /**
     * Test la suppression d'un message
     */
    public function test_user_can_delete_own_message()
    {
        // Créer une conversation
        $conversation = Conversation::create([
            'user1_id' => $this->expediteur->id,
            'user2_id' => $this->destinataire->id,
        ]);

        // Créer un message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'expediteur_id' => $this->expediteur->id,
            'destinataire_id' => $this->destinataire->id,
            'contenu' => 'Message à supprimer',
            'lu' => false,
        ]);

        $response = $this->actingAs($this->expediteur)
            ->deleteJson("/api/messaging/messages/{$message->id}");

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    /**
     * Test la récupération du compteur de messages non lus
     */
    public function test_user_can_get_unread_count()
    {
        // Créer une conversation
        $conversation = Conversation::create([
            'user1_id' => $this->expediteur->id,
            'user2_id' => $this->destinataire->id,
        ]);

        // Créer plusieurs messages non lus
        Message::create([
            'conversation_id' => $conversation->id,
            'expediteur_id' => $this->expediteur->id,
            'destinataire_id' => $this->destinataire->id,
            'contenu' => 'Message 1',
            'lu' => false,
        ]);
        
        Message::create([
            'conversation_id' => $conversation->id,
            'expediteur_id' => $this->expediteur->id,
            'destinataire_id' => $this->destinataire->id,
            'contenu' => 'Message 2',
            'lu' => false,
        ]);

        $response = $this->actingAs($this->destinataire)
            ->getJson('/api/messaging/unread-count');

        $response->assertStatus(200)
            ->assertJsonPath('data.unread_count', 2);
    }
}