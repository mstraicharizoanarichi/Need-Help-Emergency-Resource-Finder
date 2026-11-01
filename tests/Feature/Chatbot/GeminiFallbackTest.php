<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GeminiFallbackTest extends TestCase
{
    public function test_chatbot_operates_reliably_without_gemini_api_key(): void
    {
        Config::set('services.gemini.api_key', null);

        $conversation = ChatConversation::create([
            'session_id' => 'test-offline-gemini',
            'language' => 'en',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'Difficulty breathing and chest tightness',
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertNotEmpty($data['message']);
        $this->assertTrue($data['is_emergency']);
        $this->assertEquals('P1', $data['severity_level']);
    }
}
