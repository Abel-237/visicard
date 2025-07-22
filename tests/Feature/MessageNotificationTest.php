<?php

namespace Tests\Feature;

use App\Models\BusinessCard;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_creates_notification_in_database()
    {
        // Créer deux utilisateurs avec des cartes de visite
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        BusinessCard::factory()->create(['user_id' => $sender->id]);
        BusinessCard::factory()->create(['user_id' => $receiver->id]);

        // Envoyer un message
        $response = $this->actingAs($sender)
            ->postJson("/messages/{$receiver->id}", [
                'content' => 'Test message content'
            ]);

        $response->assertStatus(200);

        // Vérifier que le message a été créé
        $this->assertDatabaseHas('messages', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Test message content'
        ]);

        // Vérifier qu'une notification a été créée
        $this->assertDatabaseHas('notifications', [
            'user_id' => $receiver->id,
            'type' => 'message',
            'is_read' => false
        ]);
    }

    public function test_notification_is_marked_as_read_when_message_is_read()
    {
        // Créer deux utilisateurs avec des cartes de visite
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        BusinessCard::factory()->create(['user_id' => $sender->id]);
        BusinessCard::factory()->create(['user_id' => $receiver->id]);

        // Créer un message
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => 'Test message'
        ]);

        // Créer une notification
        $notification = Notification::create([
            'title' => 'Nouveau message',
            'message' => 'Test message',
            'type' => 'message',
            'user_id' => $receiver->id,
            'is_read' => false
        ]);

        // Marquer le message comme lu
        $message->markAsRead();

        // Vérifier que la notification a été marquée comme lue
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true
        ]);
    }

    public function test_users_without_business_cards_cannot_send_messages()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        // Seul le receiver a une carte de visite
        BusinessCard::factory()->create(['user_id' => $receiver->id]);

        $response = $this->actingAs($sender)
            ->postJson("/messages/{$receiver->id}", [
                'content' => 'Test message'
            ]);

        $response->assertStatus(403);
    }
} 