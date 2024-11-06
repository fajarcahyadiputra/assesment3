<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    /** @test */
    public function it_registers_a_user_successfully()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'city' => 'city',
            'country' => 'indonesia',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /** @test */
    public function it_fails_registration_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'member_code']);
    }

    /** @test */
    public function it_logs_in_a_user_successfully()
    {
        $user = User::factory()->create([
            'email' => 'fajarcahyadi@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'fajarcahyadi@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User logged in successfully',
            ]);

        $this->assertArrayHasKey('token', $response->json());
    }

    /** @test */
    public function it_fails_login_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'fajarcahyadi@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'fajarcahyadi@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Email or password is incorrect',
            ]);
    }
}