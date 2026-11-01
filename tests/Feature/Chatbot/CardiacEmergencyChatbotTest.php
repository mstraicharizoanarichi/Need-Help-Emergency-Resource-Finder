<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use Tests\TestCase;

class CardiacEmergencyChatbotTest extends TestCase
{
    public function test_chest_pain_triggers_cardiac_emergency_and_p1_status(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-cardiac-1',
            'language' => 'en',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'Severe crushing chest pain radiating to left arm with cold sweat',
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertTrue($data['is_emergency']);
        $this->assertEquals('P1', $data['severity_level']);
        $this->assertEquals('CARDIAC_EMERGENCY', $data['active_intent']);
        $this->assertTrue($data['requires_sos']);
    }

    public function test_cardiac_unconscious_answer_flags_critical_red_flag(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-cardiac-unconscious',
            'language' => 'en',
            'severity_level' => 'P2',
            'active_intent' => 'CARDIAC_EMERGENCY',
            'assessment_data' => ['current_question_key' => 'consciousness'],
            'is_emergency_active' => false,
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'unconscious',
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $conversation->refresh();
        $this->assertTrue($conversation->is_emergency_active);
        $this->assertEquals('P1', $conversation->severity_level);
        $this->assertEquals('unconscious', $conversation->assessment_data['consciousness']);
    }
}
