<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use Tests\TestCase;

class BanglishChatbotTest extends TestCase
{
    public function test_banglish_speech_loss_detects_stroke_emergency(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-banglish-stroke',
            'language' => 'bn',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'amar baba kotha bolte partese na mukh baka hoye gese',
            'language' => 'bn',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('STROKE', $data['active_intent']);
        $this->assertEquals('P1', $data['severity_level']);
        $this->assertTrue($data['is_emergency']);
    }

    public function test_banglish_chest_pain_detects_cardiac_emergency(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-banglish-cardiac',
            'language' => 'bn',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'buke betha onek beshi chap lagche',
            'language' => 'bn',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('CARDIAC_EMERGENCY', $data['active_intent']);
        $this->assertEquals('P1', $data['severity_level']);
        $this->assertTrue($data['is_emergency']);
    }
}
