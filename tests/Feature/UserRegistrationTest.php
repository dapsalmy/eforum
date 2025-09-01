<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_view_registration_form()
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 200]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 400]);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_duplicate_username()
    {
        User::factory()->create(['username' => 'existinguser']);

        $userData = [
            'name' => 'John Doe',
            'username' => 'existinguser',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 400]);
        $response->assertJsonValidationErrors(['username']);
    }

    public function test_registration_fails_with_weak_password()
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => '123', // Too short
            'password_confirmation' => '123',
            'terms' => true,
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 400]);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_with_password_mismatch()
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'terms' => true,
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 400]);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_without_terms_acceptance()
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 400]);
        $response->assertJsonValidationErrors(['terms']);
    }

    public function test_registration_creates_user_with_nigerian_data()
    {
        $userData = [
            'name' => 'Adebayo Johnson',
            'username' => 'adebayo',
            'email' => 'adebayo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_country_code' => '+234',
            'phone_number' => '8012345678',
            'state_id' => 1, // Lagos
            'lga_id' => 1, // Ikeja
            'terms' => true,
        ];

        $response = $this->post('/register', $userData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 200]);

        $this->assertDatabaseHas('users', [
            'name' => 'Adebayo Johnson',
            'username' => 'adebayo',
            'email' => 'adebayo@example.com',
            'phone_country_code' => '+234',
            'phone_number' => '8012345678',
            'state_id' => 1,
            'lga_id' => 1,
        ]);
    }

    public function test_registration_rate_limiting()
    {
        $userData = [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ];

        for ($i = 0; $i < 6; $i++) {
            $userData['email'] = "john{$i}@example.com";
            $userData['username'] = "johndoe{$i}";
            $this->post('/register', $userData);
        }

        $userData['email'] = 'final@example.com';
        $userData['username'] = 'finaluser';
        $response = $this->post('/register', $userData);

        $response->assertStatus(429); // Too Many Requests
    }
}
