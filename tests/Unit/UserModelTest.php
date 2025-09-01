<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Posts;
use App\Models\Points;
use App\Models\NigerianState;
use App\Models\NigerianLga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_formatted_phone_attribute()
    {
        $user = User::factory()->create([
            'phone_country_code' => '+234',
            'phone_number' => '8012345678'
        ]);

        $this->assertEquals('+234 8012345678', $user->formatted_phone);
    }

    public function test_user_formatted_phone_returns_null_when_no_phone()
    {
        $user = User::factory()->create([
            'phone_number' => null
        ]);

        $this->assertNull($user->formatted_phone);
    }

    public function test_user_has_posts_relationship()
    {
        $user = User::factory()->create();
        $post = Posts::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->posts->contains($post));
    }

    public function test_user_total_points_calculation()
    {
        $user = User::factory()->create();
        
        Points::create(['user_id' => $user->id, 'score' => 100, 'type' => 1]);
        Points::create(['user_id' => $user->id, 'score' => 50, 'type' => 1]);
        Points::create(['user_id' => $user->id, 'score' => -20, 'type' => 2]);

        $this->assertEquals(130, $user->total_points());
    }

    public function test_user_belongs_to_nigerian_state()
    {
        $state = NigerianState::factory()->create(['name' => 'Lagos']);
        $user = User::factory()->create(['state_id' => $state->id]);

        $this->assertEquals('Lagos', $user->state->name);
    }

    public function test_user_belongs_to_nigerian_lga()
    {
        $lga = NigerianLga::factory()->create(['name' => 'Ikeja']);
        $user = User::factory()->create(['lga_id' => $lga->id]);

        $this->assertEquals('Ikeja', $user->lga->name);
    }

    public function test_user_full_location_attribute()
    {
        $state = NigerianState::factory()->create(['name' => 'Lagos']);
        $lga = NigerianLga::factory()->create(['name' => 'Ikeja']);
        
        $user = User::factory()->create([
            'state_id' => $state->id,
            'lga_id' => $lga->id
        ]);

        $this->assertEquals('Ikeja, Lagos', $user->full_location);
    }

    public function test_user_full_location_fallback_to_state_only()
    {
        $state = NigerianState::factory()->create(['name' => 'Lagos']);
        
        $user = User::factory()->create([
            'state_id' => $state->id,
            'lga_id' => null
        ]);

        $this->assertEquals('Lagos', $user->full_location);
    }

    public function test_user_is_verified_professional()
    {
        $user = User::factory()->create([
            'verified' => true,
            'verification_type' => 'professional'
        ]);

        $this->assertTrue($user->isVerifiedProfessional());
    }

    public function test_user_is_not_verified_professional_without_verification_type()
    {
        $user = User::factory()->create([
            'verified' => true,
            'verification_type' => null
        ]);

        $this->assertFalse($user->isVerifiedProfessional());
    }

    public function test_user_is_trusted_contributor()
    {
        $user = User::factory()->create([
            'is_trusted_contributor' => true
        ]);

        $this->assertTrue($user->isTrustedContributor());
    }

    public function test_user_is_trusted_contributor_by_trust_score()
    {
        $user = User::factory()->create([
            'is_trusted_contributor' => false,
            'trust_score' => 85
        ]);

        $this->assertTrue($user->isTrustedContributor());
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password'
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(password_verify('plaintext-password', $user->password));
    }

    public function test_user_hidden_attributes()
    {
        $user = User::factory()->create([
            'password' => 'secret',
            'two_factor_secret' => 'secret-key',
            'two_factor_recovery_codes' => ['code1', 'code2']
        ]);

        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
        $this->assertArrayNotHasKey('two_factor_secret', $userArray);
        $this->assertArrayNotHasKey('two_factor_recovery_codes', $userArray);
    }
}
