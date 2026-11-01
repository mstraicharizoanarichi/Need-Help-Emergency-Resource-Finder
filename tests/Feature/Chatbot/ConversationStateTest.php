<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatAssessment;
use App\Models\ChatConversation;
use Tests\TestCase;

class ConversationStateTest extends TestCase
{
    public function test_conversation_completes_assessment_and_persists_assessment_record(): void
    {
        // Seizure flow has 2 questions: currently_seizing -> duration
        $conversation = ChatConversation::create([
            'session_id' => 'test-seizure-flow',
            'language' => 'en',
            'severity_level' => 'P1',
            'active_intent' => 'SEIZURE',
            'assessment_data' => ['current_question_key' => 'currently_seizing'],
            'is_emergency_active' => true,
            'status' => 'ACTIVE',
        ]);

        // Answer 1: currently_seizing
        $response1 = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'active_now',
            'language' => 'en',
        ]);

        $response1->assertStatus(200);
        $conversation->refresh();
        $this->assertEquals('duration', $conversation->assessment_data['current_question_key']);

        // Answer 2: duration -> over_5m (last question)
        $response2 = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'over_5m',
            'language' => 'en',
        ]);

        $response2->assertStatus(200);

        // Verify ChatAssessment model in database
        $this->assertDatabaseHas('chat_assessments', [
            'conversation_id' => $conversation->id,
            'incident_type' => 'SEIZURE',
            'severity' => 'P1',
        ]);

        $assessment = ChatAssessment::where('conversation_id', $conversation->id)->first();
        $this->assertNotNull($assessment);
        $this->assertEquals('active_now', $assessment->answers['currently_seizing']);
        $this->assertEquals('over_5m', $assessment->answers['duration']);
    }
}
