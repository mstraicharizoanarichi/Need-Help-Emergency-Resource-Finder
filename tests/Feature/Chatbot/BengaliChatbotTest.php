<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use Tests\TestCase;

class BengaliChatbotTest extends TestCase
{
    public function test_bengali_stroke_query_detects_stroke_intent_and_responds_in_bengali(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-bn-stroke',
            'language' => 'bn',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'আমার বাবার মুখ একপাশে বেঁকে গেছে এবং কথা বলতে পারছে না',
            'language' => 'bn',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('bn', $data['language']);
        $this->assertEquals('STROKE', $data['active_intent']);
        $this->assertEquals('P1', $data['severity_level']);
        $this->assertTrue($data['is_emergency']);
        $this->assertStringContainsString('জরুরি সতর্কতা', $data['message']);

        // Check options in Bengali
        $this->assertNotEmpty($data['options']);
        $this->assertStringContainsString('সচেতন', $data['options'][0]['label']);
    }

    public function test_bengali_knowledge_query_returns_curated_guidance(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-bn-knowledge',
            'language' => 'bn',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'স্ট্রোকের লক্ষণ কী কী?',
            'language' => 'bn',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('bn', $data['language']);
        $this->assertStringContainsString('FAST', $data['message']);
        $this->assertStringContainsString('মুখ', $data['message']);
    }
}
