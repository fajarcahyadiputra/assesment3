<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test update method - Update an existing user.
     *
     * @return void
     */
    public function test_update_user()
    {
        $user = User::create([
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        $existingUser = User::create([
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'city' => 'city',
            'country' => 'indonesia',
            'password' => bcrypt('password123'),
        ]);

        $updatedUserData = [
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'city' => 'city',
            'country' => 'indonesia',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'member_code' => 'DIS101',
        ];
        $user_id = $existingUser->id;

        $response = $this->putJson("/api/users/{$user_id}", $updatedUserData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
                'data' => [
                    'name' => 'fajar cahyadi',
                    'email' => 'fajarcahyadi@example.com',
                ]
            ]);
    }

    /**
     * Test destroy method - Delete a user.
     *
     * @return void
     */
    public function test_destroy_user()
    {
        // Arrange: Create a user for authentication
        $user = User::create([
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        // Generate JWT token using JWTAuth::attempt() with email and password
        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        // Arrange: Manually create a user to be deleted
        $userToDelete = User::create([
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'EIS202',
        ]);

        // Act: Send a DELETE request with the Bearer token in the header
        $response = $this->deleteJson("/api/users/{$userToDelete->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert: Check if the user was deleted successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User deleted successfully',
            ]);
    }

    /**
     * Test index method - Retrieve users with pagination and search.
     *
     * @return void
     */
    public function test_index_users()
    {
        $user = User::create([
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        User::create([
            'name' => 'First User',
            'email' => 'firstuser@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'FIS001',
        ]);
        User::create([
            'name' => 'Second User',
            'email' => 'seconduser@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'SIS002',
        ]);

        $response = $this->getJson('/api/users?page=1&limit=2', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test show method - Retrieve a specific user.
     *
     * @return void
     */
    public function test_show_user()
    {
        $user = User::create([
            'name' => 'fajar cahyadi',
            'email' => 'fajarcahyadi@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'AIS123',
        ]);

        $token = JWTAuth::attempt(['email' => 'user@example.com', 'password' => 'password123']);

        $userToFetch = User::create([
            'name' => 'User to Fetch',
            'email' => 'usertofetch@example.com',
            'password' => bcrypt('password123'),
            'member_code' => 'BIS103',
        ]);

        $response = $this->getJson("/api/users/{$userToFetch->id}", [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User fetched successfully',
                'data' => [
                    'name' => 'User to Fetch',
                    'email' => 'usertofetch@example.com',
                    'member_code' => 'BIS103',
                ]
            ]);
    }
}
