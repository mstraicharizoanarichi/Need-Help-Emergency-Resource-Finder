<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use App\Models\EmergencyIncident;
use Tests\TestCase;

class SOSIntegrationTest extends TestCase
{
    public function test_sos_triggered_from_chat_creates_incident_and_links_to_conversation(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-sos-link',
            'language' => 'en',
            'severity_level' => 'P1',
            'active_intent' => 'STROKE',
            'is_emergency_active' => true,
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.sos', $conversation), [
            'latitude' => 23.7925,
            'longitude' => 90.4078,
            'address' => 'Banani, Dhaka',
            'reporter_name' => 'Emergency Citizen',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'reference',
            'tracking_url',
            'incident' => ['reference', 'severity', 'status'],
        ]);

        $reference = $response->json('reference');
        $this->assertMatchesRegularExpression('/^NH-\d{4}-\d{6}$/', $reference);

        // Verify incident in database
        $incident = EmergencyIncident::where('public_reference', $reference)->first();
        $this->assertNotNull($incident);
        $this->assertTrue((bool) $incident->is_sos);
        $this->assertEquals('P1', $incident->severity_level);
        $this->assertEquals('STROKE', $incident->emergency_type);

        // Verify conversation is linked to incident
        $conversation->refresh();
        $this->assertEquals($incident->id, $conversation->emergency_incident_id);

        // Verify assistant message confirms SOS
        $this->assertDatabaseHas('chat_messages', [
            'conversation_id' => $conversation->id,
            'sender' => 'assistant',
        ]);
    }
}
