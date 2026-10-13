<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\Resume;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_application_flow(): void
    {
        $vacancy = Vacancy::factory()->create();
        $employer = $vacancy->company->user;
        $seeker = User::factory()->create();
        $resume = Resume::factory()->for($seeker)->private()->create();

        $appId = $this->actingAs($seeker)->postJson("/api/v1/vacancies/{$vacancy->id}/apply", [
            'resume_id' => $resume->id, 'cover_letter' => 'Salom',
        ])->assertCreated()->assertJsonPath('data.status.value', 'pending')->json('data.id');

        $this->actingAs($seeker)->postJson("/api/v1/vacancies/{$vacancy->id}/apply")->assertUnprocessable();
        $this->actingAs($seeker)->getJson("/api/v1/vacancies/{$vacancy->id}")->assertJsonPath('data.has_applied', true);

        $this->actingAs($employer)->getJson("/api/v1/vacancies/{$vacancy->id}/applications")->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($employer)->getJson('/api/v1/me/incoming-applications')->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($employer)->getJson("/api/v1/resumes/{$resume->id}")->assertOk();

        $this->actingAs($employer)->patchJson("/api/v1/applications/{$appId}", ['status' => 'accepted'])
            ->assertOk()->assertJsonPath('data.status.value', 'accepted');

        $this->actingAs($seeker)->getJson('/api/v1/me/applications')->assertJsonPath('data.0.status.value', 'accepted');
    }

    public function test_cannot_use_someone_elses_resume(): void
    {
        $vacancy = Vacancy::factory()->create();
        $foreign = Resume::factory()->create();

        $this->actingAs(User::factory()->create())->postJson("/api/v1/vacancies/{$vacancy->id}/apply", ['resume_id' => $foreign->id])
            ->assertUnprocessable()->assertJsonValidationErrors('resume_id');
    }

    public function test_cannot_apply_to_closed_vacancy(): void
    {
        $vacancy = Vacancy::factory()->closed()->create();

        $this->actingAs(User::factory()->create())->postJson("/api/v1/vacancies/{$vacancy->id}/apply")->assertUnprocessable();
    }

    public function test_other_employer_cannot_change_status_or_view(): void
    {
        $application = Application::factory()->create();
        $other = Company::factory()->create()->user;

        $this->actingAs($other)->patchJson("/api/v1/applications/{$application->id}", ['status' => 'accepted'])->assertForbidden();
        $this->actingAs($other)->getJson("/api/v1/vacancies/{$application->vacancy_id}/applications")->assertForbidden();
    }

    public function test_seeker_can_withdraw_only_own_application(): void
    {
        $application = Application::factory()->create();

        $this->actingAs(User::factory()->create())->deleteJson("/api/v1/applications/{$application->id}")->assertForbidden();
        $this->actingAs($application->user)->deleteJson("/api/v1/applications/{$application->id}")->assertNoContent();
    }
}
