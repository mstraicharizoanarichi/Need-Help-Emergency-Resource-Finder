<?php

namespace Tests\Feature\Chatbot;

use App\Models\Category;
use App\Models\ChatConversation;
use App\Models\Resource;
use Tests\TestCase;

class HospitalIntegrationTest extends TestCase
{
    public function test_can_fetch_nearby_hospitals_endpoint(): void
    {
        $response = $this->getJson(route('emergency-assistant.resources.nearby', [
            'lat' => 23.7808,
            'lng' => 90.4152,
            'limit' => 3,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'hospitals',
        ]);
    }

    public function test_emergency_query_with_coordinates_returns_hospital_recommendations(): void
    {
        $conversation = ChatConversation::create([
            'session_id' => 'test-hosp-coords',
            'language' => 'en',
            'severity_level' => 'P3',
            'status' => 'ACTIVE',
        ]);

        $response = $this->postJson(route('emergency-assistant.message'), [
            'conversation_id' => $conversation->id,
            'message' => 'Chest pain and need nearest hospital urgently',
            'language' => 'en',
            'latitude' => 23.7808,
            'longitude' => 90.4152,
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertArrayHasKey('hospitals', $data);
        $this->assertTrue($data['is_emergency']);
    }
}
