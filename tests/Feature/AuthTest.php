<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeker_can_register_with_local_phone_format(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ali Valiyev',
            'phone' => '90 123 45 67',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'seeker',
        ])->assertCreated()
            ->assertJsonPath('user.phone', '+998901234567')
            ->assertJsonPath('user.role.value', 'seeker')
            ->assertJsonStructure(['token']);
    }

    public function test_employer_registration_creates_company(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'HR',
            'phone' => '+998901234567',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'employer',
            'company_name' => 'Test MChJ',
        ])->assertCreated()->assertJsonPath('user.company.name', 'Test MChJ');
    }

    public function test_employer_registration_requires_company_name(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'HR', 'phone' => '+998901234567', 'password' => 'secret123',
            'password_confirmation' => 'secret123', 'role' => 'employer',
        ])->assertUnprocessable()->assertJsonValidationErrors('company_name');
    }

    public function test_duplicate_phone_is_rejected(): void
    {
        User::factory()->create(['phone' => '+998901234567']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'X', 'phone' => '+998901234567', 'password' => 'secret123',
            'password_confirmation' => 'secret123', 'role' => 'seeker',
        ])->assertUnprocessable()->assertJsonValidationErrors('phone');
    }

    public function test_login_logout_and_me(): void
    {
        $user = User::factory()->create(['phone' => '+998901234567']);

        $this->postJson('/api/v1/auth/login', ['phone' => '+998901234567', 'password' => 'wrong'])
            ->assertUnprocessable();

        $token = $this->postJson('/api/v1/auth/login', ['phone' => '+998901234567', 'password' => 'password'])
            ->assertOk()->json('token');

        $this->withToken($token)->getJson('/api/v1/auth/me')->assertOk()->assertJsonPath('data.id', $user->id);
        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_password_hash_is_never_exposed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/v1/auth/me')->assertOk()->assertJsonMissingPath('data.password');
    }

    public function test_profile_update_requires_current_password_to_change_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->putJson('/api/v1/auth/profile', ['name' => 'Yangi', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertUnprocessable()->assertJsonValidationErrors('current_password');

        $this->actingAs($user)->putJson('/api/v1/auth/profile', ['name' => 'Yangi ism', 'city' => 'Buxoro'])
            ->assertOk()->assertJsonPath('data.name', 'Yangi ism');
    }

    public function test_guest_cannot_access_protected_routes(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
        $this->getJson('/api/v1/resumes')->assertUnauthorized();
    }
}
