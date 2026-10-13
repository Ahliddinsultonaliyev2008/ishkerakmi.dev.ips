<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Resume;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EngagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_like_toggle_and_saved_list(): void
    {
        $vacancy = Vacancy::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->postJson("/api/v1/vacancies/{$vacancy->id}/like")->assertJson(['liked' => true, 'likes_count' => 1]);
        $this->actingAs($user)->getJson('/api/v1/me/saved')->assertJsonCount(1, 'data')->assertJsonPath('data.0.liked', true);
        $this->actingAs($user)->postJson("/api/v1/vacancies/{$vacancy->id}/like")->assertJson(['liked' => false, 'likes_count' => 0]);
    }

    public function test_comments_crud_and_ownership(): void
    {
        $vacancy = Vacancy::factory()->create();
        $author = User::factory()->create();
        $other = User::factory()->create();

        $this->postJson("/api/v1/vacancies/{$vacancy->id}/comments", ['body' => 'Salom'])->assertUnauthorized();

        $id = $this->actingAs($author)->postJson("/api/v1/vacancies/{$vacancy->id}/comments", ['body' => 'Ish vaqti qanday?'])
            ->assertCreated()->assertJsonPath('data.author.id', $author->id)->json('data.id');

        $this->getJson("/api/v1/vacancies/{$vacancy->id}/comments")->assertOk()->assertJsonCount(1, 'data');

        $this->actingAs($other)->deleteJson("/api/v1/comments/{$id}")->assertForbidden();
        $this->actingAs($author)->putJson("/api/v1/comments/{$id}", ['body' => 'Tahrirlandi'])->assertOk();
        $this->actingAs($author)->deleteJson("/api/v1/comments/{$id}")->assertNoContent();
    }

    public function test_cannot_comment_on_private_resume(): void
    {
        $resume = Resume::factory()->private()->create();

        $this->actingAs(User::factory()->create())->postJson("/api/v1/resumes/{$resume->id}/comments", ['body' => 'Salom'])->assertNotFound();
    }

    public function test_deleting_vacancy_cascades_engagement(): void
    {
        $vacancy = Vacancy::factory()->create();
        $vacancy->comments()->create(['user_id' => User::factory()->create()->id, 'body' => 'Test']);
        $vacancy->likes()->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs($vacancy->company->user)->deleteJson("/api/v1/vacancies/{$vacancy->id}")->assertNoContent();
        $this->assertSame(0, Comment::count());
        $this->assertDatabaseCount('likes', 0);
    }
}
