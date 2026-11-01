<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthenticationAndRoleAuthorizationTest extends TestCase
{
    public function test_user_can_register_as_citizen(): void
    {
        $response = $this->post('/register', [
            'name' => 'Tanvir Ahmed',
            'email' => 'tanvir_' . uniqid() . '@example.com',
            'phone' => '+88017' . rand(10000000, 99999999),
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'user',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_can_register_as_provider(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr. Rafiqul Islam',
            'email' => 'dr_rafiq_' . uniqid() . '@hospital.com',
            'phone' => '+88018' . rand(10000000, 99999999),
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'provider',
            'organization_name' => 'Central Trauma Hospital',
            'designation' => 'Director',
        ]);

        $response->assertRedirect('/provider/dashboard');
        $this->assertAuthenticated();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Verification Queue');
        $response->assertSee('Resources by Emergency Category');
    }

    public function test_citizen_cannot_access_admin_dashboard(): void
    {
        $user = User::where('role', 'user')->first();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_suspended_user_is_blocked_by_active_middleware(): void
    {
        $suspendedUser = User::factory()->create([
            'role' => 'user',
            'status' => 'suspended',
        ]);

        $response = $this->actingAs($suspendedUser)->get('/dashboard');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
    }
}
