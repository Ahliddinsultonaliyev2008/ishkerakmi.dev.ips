<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacancyTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Laravel dasturchi',
            'description' => 'Tavsif',
            'city' => 'Toshkent',
            'employment_type' => 'full_time',
            'experience' => '1_3',
            'salary_from' => 8000000,
            'salary_to' => 12000000,
            'skills' => ['PHP', 'Laravel'],
            ...$overrides,
        ];
    }

    public function test_public_list_shows_only_active_vacancies_and_filters(): void
    {
        $it = Category::factory()->create(['slug' => 'it']);
        Vacancy::factory()->create(['title' => 'PHP dasturchi', 'city' => 'Toshkent', 'category_id' => $it->id]);
        Vacancy::factory()->create(['title' => 'Oshpaz', 'city' => 'Buxoro']);
        Vacancy::factory()->closed()->create(['title' => 'Yopiq']);

        $this->getJson('/api/v1/vacancies')->assertOk()->assertJsonCount(2, 'data')->assertJsonStructure(['data', 'links', 'meta']);
        $this->getJson('/api/v1/vacancies?q=PHP')->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'PHP dasturchi');
        $this->getJson('/api/v1/vacancies?city=Buxoro')->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Oshpaz');
        $this->getJson('/api/v1/vacancies?category=it')->assertJsonCount(1, 'data');
    }

    public function test_show_increments_views(): void
    {
        $vacancy = Vacancy::factory()->create(['views' => 0]);

        $this->getJson("/api/v1/vacancies/{$vacancy->id}")->assertOk()->assertJsonPath('data.views', 1)
            ->assertJsonPath('data.employment_type.value', $vacancy->employment_type->value);
    }

    public function test_employer_can_manage_own_vacancies(): void
    {
        $company = Company::factory()->create();
        $user = $company->user;

        $id = $this->actingAs($user)->postJson('/api/v1/vacancies', $this->payload())
            ->assertCreated()->assertJsonPath('data.company.id', $company->id)->json('data.id');

        $this->actingAs($user)->putJson("/api/v1/vacancies/{$id}", $this->payload(['title' => 'Senior', 'status' => 'closed']))
            ->assertOk()->assertJsonPath('data.title', 'Senior')->assertJsonPath('data.status.value', 'closed');

        $this->actingAs($user)->getJson('/api/v1/me/vacancies')->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($user)->deleteJson("/api/v1/vacancies/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('vacancies', ['id' => $id]);
    }

    public function test_employer_cannot_touch_other_company_vacancy(): void
    {
        $vacancy = Vacancy::factory()->create();
        $other = Company::factory()->create()->user;

        $this->actingAs($other)->putJson("/api/v1/vacancies/{$vacancy->id}", $this->payload())->assertForbidden();
        $this->actingAs($other)->deleteJson("/api/v1/vacancies/{$vacancy->id}")->assertForbidden();
    }

    public function test_seeker_cannot_create_vacancy(): void
    {
        $this->actingAs(User::factory()->create())->postJson('/api/v1/vacancies', $this->payload())->assertForbidden();
    }

    public function test_validation_errors(): void
    {
        $user = Company::factory()->create()->user;

        $this->actingAs($user)->postJson('/api/v1/vacancies', $this->payload(['salary_to' => 1, 'employment_type' => 'xyz']))
            ->assertUnprocessable()->assertJsonValidationErrors(['salary_to', 'employment_type']);
    }

    public function test_employer_can_update_company_profile(): void
    {
        $user = Company::factory()->create()->user;

        $this->actingAs($user)->putJson('/api/v1/me/company', ['name' => 'Yangi nom', 'city' => 'Samarqand', 'phone' => '901234567'])
            ->assertOk()->assertJsonPath('data.name', 'Yangi nom')->assertJsonPath('data.phone', '+998901234567');

        $this->getJson('/api/v1/companies')->assertOk()->assertJsonPath('data.0.name', 'Yangi nom');
    }

    public function test_meta_endpoint(): void
    {
        Vacancy::factory(2)->create();

        $this->getJson('/api/v1/meta')->assertOk()
            ->assertJsonPath('stats.vacancies', 2)
            ->assertJsonStructure(['cities', 'categories', 'employment_types', 'experiences', 'stats']);
    }
}
