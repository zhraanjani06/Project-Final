<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiskIntelligenceTest extends TestCase
{
    /**
     * Test that guest users are redirected to login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $apiResponse = $this->get('/api/countries');
        $apiResponse->assertStatus(401);
    }

    /**
     * Test that authenticated users can access the dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        // Get user from seeded database or create a mock one
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
    }

    /**
     * Test that internal API endpoints return expected structure.
     */
    public function test_countries_api_endpoint(): void
    {
        $user = User::factory()->create();
        
        // Ensure at least one country exists
        Country::firstOrCreate(
            ['code' => 'ID'],
            [
                'name' => 'Indonesia',
                'region' => 'Asia',
                'currency_code' => 'IDR',
                'latitude' => -0.789,
                'longitude' => 113.921
            ]
        );

        $response = $this->actingAs($user)->get('/api/countries');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => [
                         'id',
                         'code',
                         'name',
                         'region',
                         'currency_code',
                         'latitude',
                         'longitude',
                         'risk_score',
                         'risk_level'
                     ]
                 ]);
    }

    /**
     * Test that country detail assessment endpoint returns full intelligence metrics.
     */
    public function test_country_assessment_endpoint(): void
    {
        $user = User::factory()->create();
        
        Country::firstOrCreate(
            ['code' => 'ID'],
            [
                'name' => 'Indonesia',
                'region' => 'Asia',
                'currency_code' => 'IDR',
                'latitude' => -0.789,
                'longitude' => 113.921
            ]
        );

        $response = $this->actingAs($user)->get('/api/countries/ID');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'country',
                     'profile',
                     'weather',
                     'economy',
                     'currency',
                     'news',
                     'risk'
                 ]);
    }

    /**
     * Test that non-admin users cannot access admin endpoints.
     */
    public function test_non_admin_cannot_access_admin_endpoints(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get('/admin')->assertStatus(403);
        $this->actingAs($user)->post('/admin/ports', ['name' => 'Test Port'])->assertStatus(403);
    }

    /**
     * Test that admin can view admin panel, add and delete ports.
     */
    public function test_admin_can_manage_ports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $country = Country::firstOrCreate(
            ['code' => 'ID'],
            [
                'name' => 'Indonesia',
                'region' => 'Asia',
                'currency_code' => 'IDR',
                'latitude' => -0.789,
                'longitude' => 113.921
            ]
        );

        // Can access admin panel
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);

        // Can add port
        $postResponse = $this->actingAs($admin)->post('/admin/ports', [
            'name' => 'Test Rotterdam Port',
            'country_code' => 'ID',
            'latitude' => 1.234,
            'longitude' => 5.678
        ]);
        $postResponse->assertRedirect();
        
        $this->assertDatabaseHas('ports', [
            'name' => 'Test Rotterdam Port',
            'country_code' => 'ID'
        ]);

        $portId = \App\Models\Port::where('name', 'Test Rotterdam Port')->first()->id;

        // Can delete port
        $deleteResponse = $this->actingAs($admin)->delete("/admin/ports/{$portId}");
        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('ports', [
            'id' => $portId
        ]);
    }
}
