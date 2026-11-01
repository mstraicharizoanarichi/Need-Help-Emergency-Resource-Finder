<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use Tests\TestCase;

class StrokeChatbotTest extends TestCase
{
    public function test_stroke_indicators_trigger_p1_emergency_mode_and_fast_questions(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-stroke-1',
            'language' => 'en',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'My father suddenly has facial droop and slurred speech, he cannot lift his right arm',
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertTrue($data['is_emergency']);
        $this->assertEquals('P1', $data['severity_level']);
        $this->assertEquals('STROKE', $data['active_intent']);
        $this->assertTrue($data['requires_sos']);
        $this->assertNotEmpty($data['options']);

        // Assert database state updated
        $conversation->refresh();
        $this->assertTrue($conversation->is_emergency_active);
        $this->assertEquals('P1', $conversation->severity_level);
        $this->assertEquals('STROKE', $conversation->active_intent);
    }

    public function test_stroke_multi_turn_answering_progresses_through_assessment(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-stroke-turns',
            'language' => 'en',
            'severity_level' => 'P1',
            'active_intent' => 'STROKE',
            'assessment_data' => ['current_question_key' => 'consciousness'],
            'is_emergency_active' => true,
            'status' => 'ACTIVE',
        ]);

        // Answer question 1: consciousness -> drowsy
        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'drowsy',
            'language' => 'en',
        ]);

        $response->assertStatus(200);
        $conversation->refresh();
        $this->assertEquals('drowsy', $conversation->assessment_data['consciousness']);
        $this->assertEquals('onset_time', $conversation->assessment_data['current_question_key']);

        // Answer question 2: onset_time -> under_30m
        $response2 = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'under_30m',
            'language' => 'en',
        ]);

        $response2->assertStatus(200);
        $conversation->refresh();
        $this->assertEquals('under_30m', $conversation->assessment_data['onset_time']);
        $this->assertEquals('fast_symptoms', $conversation->assessment_data['current_question_key']);
    }
}
