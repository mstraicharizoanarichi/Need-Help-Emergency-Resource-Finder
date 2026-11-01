<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function test_rejects_empty_message(): void
    {
        $response = $this->postJson(route('emergency-assistant.message'), [
            'message' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_rejects_excessively_long_message(): void
    {
        $longMessage = str_repeat('Emergency Help Needed! ', 200); // > 4000 chars

        $response = $this->postJson(route('emergency-assistant.message'), [
            'message' => $longMessage,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_handles_non_existent_conversation_id_gracefully(): void
    {
        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => 9999999, // Non-existent
            'message' => 'Is anyone there?',
        ]);

        // FormRequest exists rule rejects invalid ID with 422
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['conversation_id']);
    }

    public function test_sanitizes_potential_xss_content_safely(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-xss',
            'language' => 'en',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $xssPayload = '<script>alert("hack")</script> What are the signs of burn?';

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => $xssPayload,
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'message' => $xssPayload,
        ]);
    }
}
