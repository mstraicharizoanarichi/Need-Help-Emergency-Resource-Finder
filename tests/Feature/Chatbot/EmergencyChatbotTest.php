<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Tests\TestCase;

class EmergencyChatbotTest extends TestCase
{
    public function test_can_access_emergency_assistant_page(): void
    {
        $response = $this->get(route('emergency-assistant.index'));

        $response->assertStatus(200);
        $response->assertSee('Emergency Assistant');
        $response->assertSee('AI Health Triage');
    }

    public function test_can_initialize_chat_conversation(): void
    {
        $response = $this->postJson(route('emergency-assistant.conversations.init'), [
            'language' => 'bn',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'conversation' => [
                'language' => 'bn',
                'severity_level' => 'P3',
                'is_emergency_active' => false,
            ],
        ]);

        $this->assertDatabaseHas('chat_conversations', [
            'id' => $response->json('conversation.id'),
            'language' => 'bn',
        ]);
    }

    public function test_can_send_message_and_receive_assistant_response(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-session-123',
            'language' => 'en',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'Hello, I have a general health question about first aid.',
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'conversation_id',
                'message',
                'sender',
                'language',
                'severity_level',
            ],
        ]);

        // User message stored
        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => 'Hello, I have a general health question about first aid.',
        ]);

        // Assistant message stored
        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'sender' => 'assistant',
        ]);
    }

    public function test_can_retrieve_conversation_history(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-session-hist',
            'language' => 'bn',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => 'টেস্ট বার্তা',
        ]);

        $response = $this->getJson(route('emergency-assistant.conversations.show', $conversation));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'conversation' => [
                'id' => $conversation->id,
                'messages' => [
                    ['message' => 'টেস্ট বার্তা'],
                ],
            ],
        ]);
    }

    public function test_can_switch_language(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-session-lang',
            'language' => 'bn',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.language', $conversation), [
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'language' => 'en',
        ]);

        $this->assertDatabaseHas('chat_conversations', [
            'id' => $conversation->id,
            'language' => 'en',
        ]);
    }
}
