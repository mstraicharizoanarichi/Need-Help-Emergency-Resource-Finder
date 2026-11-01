<?php

namespace Tests\Feature;

use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use Tests\TestCase;

class ProviderResourceManagementTest extends TestCase
{
    public function test_provider_can_create_new_emergency_resource(): void
    {
        $provider = User::where('role', 'provider')->first();
        $category = ResourceCategory::first();

        $response = $this->actingAs($provider)->post('/provider/resources', [
            'category_id' => $category->id,
            'name' => 'Provider Test Ambulance Fleet',
            'phone' => '+8801999887766',
            'emergency_phone' => '16111',
            'address' => 'House 45, Road 7, Banani',
            'area' => 'Banani',
            'city' => 'Dhaka',
            'district' => 'Dhaka',
            'latitude' => 23.7937,
            'longitude' => 90.4066,
            'is_24_7' => 1,
            'ambulance_available' => 1,
            'oxygen_available' => 1,
        ]);

        $response->assertRedirect('/provider/resources');
        $this->assertDatabaseHas('resources', [
            'name' => 'Provider Test Ambulance Fleet',
            'user_id' => $provider->id,
            'verification_status' => 'pending',
        ]);
    }

    public function test_provider_cannot_edit_another_providers_resource(): void
    {
        $providerA = User::where('role', 'provider')->first();
        $providerB = User::factory()->create([
            'role' => 'provider',
            'email' => 'other_prov_' . uniqid() . '@needhelp.bd',
        ]);

        $resourceA = Resource::where('user_id', $providerA->id)->first();

        if ($resourceA) {
            $response = $this->actingAs($providerB)->get("/provider/resources/{$resourceA->id}/edit");
            $response->assertStatus(403);
        }
    }

    public function test_provider_can_toggle_availability_status(): void
    {
        $provider = User::where('role', 'provider')->first();
        $resource = Resource::where('user_id', $provider->id)->first();

        if ($resource) {
            $initialStatus = $resource->availability_status;

            $response = $this->actingAs($provider)->post("/provider/resources/{$resource->id}/availability");
            $response->assertRedirect();

            $resource->refresh();
            $this->assertNotEquals($initialStatus, $resource->availability_status);
        }
    }
}
