<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }



    /** @test */
    public function registration_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function registration_fails_with_invalid_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Invalid Email',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
