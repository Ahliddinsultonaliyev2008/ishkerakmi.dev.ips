<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Dasturchi',
            'full_name' => 'Ali Valiyev',
            'phone' => '+998901234567',
            'city' => 'Toshkent',
            'employment_type' => 'remote',
            'experience' => 'none',
            'skills' => ['PHP'],
            ...$overrides,
        ];
    }

    public function test_seeker_crud_own_resume(): void
    {
        $user = User::factory()->create();

        $id = $this->actingAs($user)->postJson('/api/v1/resumes', $this->payload())->assertCreated()->json('data.id');
        $this->actingAs($user)->putJson("/api/v1/resumes/{$id}", $this->payload(['title' => 'Yangi']))->assertOk()->assertJsonPath('data.title', 'Yangi');
        $this->actingAs($user)->getJson('/api/v1/me/resumes')->assertJsonCount(1, 'data');
        $this->actingAs($user)->deleteJson("/api/v1/resumes/{$id}")->assertNoContent();
    }

    public function test_seeker_cannot_modify_others_resume(): void
    {
        $resume = Resume::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other)->putJson("/api/v1/resumes/{$resume->id}", $this->payload())->assertForbidden();
        $this->actingAs($other)->deleteJson("/api/v1/resumes/{$resume->id}")->assertForbidden();
    }

    public function test_employer_cannot_create_resume(): void
    {
        $this->actingAs(User::factory()->employer()->create())->postJson('/api/v1/resumes', $this->payload())->assertForbidden();
    }

    public function test_private_resumes_are_hidden_and_contacts_visible_only_to_employers(): void
    {
        $public = Resume::factory()->create();
        $private = Resume::factory()->private()->create();
        $seeker = User::factory()->create();
        $employer = Company::factory()->create()->user;

        $this->actingAs($seeker)->getJson('/api/v1/resumes')->assertJsonCount(1, 'data');
        $this->actingAs($seeker)->getJson("/api/v1/resumes/{$private->id}")->assertForbidden();
        $this->actingAs($seeker)->getJson("/api/v1/resumes/{$public->id}")->assertOk()->assertJsonMissingPath('data.phone');
        $this->actingAs($employer)->getJson("/api/v1/resumes/{$public->id}")->assertOk()->assertJsonPath('data.phone', $public->phone);
    }
}
