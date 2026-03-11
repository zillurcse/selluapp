<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PINAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_set_pin()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/pin/set', [
                'pin' => '12345',
            ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('12345', $user->fresh()->security_pin));
    }

    public function test_user_cannot_set_pin_with_invalid_length()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/pin/set', [
                'pin' => '1234',
            ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login_with_valid_pin()
    {
        $user = User::factory()->create([
            'security_pin' => Hash::make('54321'),
        ]);

        $response = $this->postJson('/api/login-pin', [
            'email' => $user->email,
            'pin' => '54321',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user'
            ]);
    }

    public function test_user_cannot_login_with_invalid_pin()
    {
        $user = User::factory()->create([
            'security_pin' => Hash::make('54321'),
        ]);

        $response = $this->postJson('/api/login-pin', [
            'email' => $user->email,
            'pin' => '11111',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pin']);
    }
}
