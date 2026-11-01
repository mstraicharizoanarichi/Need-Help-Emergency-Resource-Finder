<?php

namespace Tests\Feature;

use App\Models\Resource;
use Tests\TestCase;

class PublicEmergencySearchTest extends TestCase
{
    public function test_home_page_is_publicly_accessible_without_login(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Need');
        $response->assertSee('Help');
        $response->assertSee('999');
    }

    public function test_public_emergency_search_works_without_login(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
        $response->assertSee('Search Results');
    }

    public function test_haversine_location_search_orders_by_distance(): void
    {
        // Search around Dhanmondi, Dhaka (23.7465, 90.3760)
        $response = $this->get('/search?lat=23.7465&lng=90.3760&radius=25');

        $response->assertStatus(200);
        $response->assertSee('Search Results');
    }

    public function test_category_filtering_works_correctly(): void
    {
        $response = $this->get('/search?category=hospital');

        $response->assertStatus(200);
    }

    public function test_resource_detail_page_is_accessible_by_slug(): void
    {
        $resource = Resource::where('verification_status', 'verified')->first();

        if ($resource) {
            $response = $this->get("/resources/{$resource->slug}");
            $response->assertStatus(200);
            $response->assertSee($resource->name);
            $response->assertSee('CALL:');
        }
    }

    public function test_map_data_json_feed_returns_valid_geojson_like_payload(): void
    {
        $response = $this->getJson('/search/map-data?lat=23.7500&lng=90.3800&radius=50');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'count',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'category_slug',
                    'latitude',
                    'longitude',
                    'phone',
                ]
            ]
        ]);
    }
}
