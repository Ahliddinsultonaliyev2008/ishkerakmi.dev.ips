<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private array $resume = [
        'firstname' => 'Ali',
        'lastname' => 'Valiyev',
        'profession' => 'Developer',
        'title' => 'Backend developer',
        'address' => 'Toshkent',
        'type' => 'full-time',
        'gender' => 'male',
        'experience' => '2 yil',
        'pay' => '1000',
        'phone' => '901231212',
    ];

    private array $job = [
        'name' => 'PHP developer',
        'profession' => 'Developer',
        'type' => 'full-time',
        'address' => 'Toshkent',
        'pay' => 1500,
        'phone' => '901231212',
        'email' => 'hr@example.com',
        'experience' => '1 yil',
        'language' => 'uz',
    ];

    private function company(string $admin = 'acme', string $password = 'secret123'): Company
    {
        return Company::create([
            'name' => 'Acme',
            'image' => 'logo.png',
            'email' => "$admin@example.com",
            'password' => Hash::make($password),
            'profession' => 'IT',
            'admin' => $admin,
        ]);
    }

    private function basicAuth(string $admin = 'acme', string $password = 'secret123'): array
    {
        return ['Authorization' => 'Basic ' . base64_encode("$admin:$password")];
    }

    public function test_user_can_register_and_login(): void
    {
        $this->postJson('/api/register', [
            'firstname' => 'Ali',
            'phone' => 901231212,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])->assertCreated()->assertJsonStructure(['token', 'user_id']);

        $this->postJson('/api/register', [
            'firstname' => 'Vali',
            'phone' => 901231212,
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])->assertStatus(422);

        $this->postJson('/api/login', ['phone' => 901231212, 'password' => '12345678'])
            ->assertOk()->assertJsonStructure(['token', 'user_id']);
    }

    public function test_user_can_manage_only_own_resume(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        Sanctum::actingAs($owner);
        $id = $this->postJson('/api/resume/store', $this->resume)->assertCreated()->json('id');
        $this->postJson('/api/resume/store', ['firstname' => 'Ali'])->assertStatus(422);

        Sanctum::actingAs($other);
        $this->putJson("/api/resume/$id", $this->resume)->assertNotFound();
        $this->deleteJson("/api/resume/$id")->assertNotFound();

        Sanctum::actingAs($owner);
        $this->putJson("/api/resume/$id", ['title' => 'Senior'] + $this->resume)->assertOk();
        $this->assertSame('Senior', Resume::find($id)->title);
        $this->deleteJson("/api/resume/$id")->assertOk();
    }

    public function test_user_can_comment_and_like_resume(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $resumeId = $this->postJson('/api/resume/store', $this->resume)->json('id');

        $this->postJson('/api/comment/resume', ['resume_id' => $resumeId, 'content' => 'Zo\'r'])->assertOk();
        $this->getJson("/api/comment/resume?resume_id=$resumeId")->assertOk()->assertJsonCount(1, 'comments');

        $likeId = $this->postJson('/api/like/resume', ['resume_id' => $resumeId, 'likes' => 1])->assertOk()->json('id');
        $this->deleteJson("/api/like/resume/$likeId")->assertOk();

        $this->postJson('/api/like/resume', ['resume_id' => $resumeId, 'likes' => 1])->assertOk();
        $this->deleteJson("/api/resume/$resumeId")->assertOk();
    }

    public function test_company_can_register_and_login(): void
    {
        $this->postJson('/api/register/company', [
            'name' => 'Acme',
            'image' => 'logo.png',
            'email' => 'acme@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'profession' => 'IT',
            'admin' => 'acme',
        ])->assertCreated()->assertJsonMissingPath('password');

        $this->postJson('/api/login/company', ['admin' => 'acme', 'password' => 'secret123'])->assertOk();
        $this->postJson('/api/login/company', ['admin' => 'acme', 'password' => 'wrong-pass'])->assertUnauthorized();
    }

    public function test_company_routes_require_basic_auth(): void
    {
        $this->company();

        $this->postJson('/api/company/profile')->assertUnauthorized();
        $this->postJson('/api/company/profile', [], $this->basicAuth('acme', 'wrong'))->assertUnauthorized();

        $this->postJson('/api/company/profile', [], $this->basicAuth())
            ->assertOk()
            ->assertJsonPath('company.admin', 'acme')
            ->assertJsonMissingPath('company.password');
    }

    public function test_company_can_manage_only_own_jobs_comments_and_likes(): void
    {
        $this->company();
        $this->company('other');

        $id = $this->postJson('/api/company/resume/store', $this->job, $this->basicAuth())->assertCreated()->json('id');
        $this->getJson("/api/company/resume/$id", $this->basicAuth())->assertOk();

        $this->putJson("/api/company/resume/$id", $this->job, $this->basicAuth('other'))->assertNotFound();
        $this->putJson("/api/company/resume/$id", ['pay' => 2000] + $this->job, $this->basicAuth())->assertOk();

        $commentId = $this->postJson('/api/company/comment/job', ['job_id' => $id, 'content' => 'Yangi'], $this->basicAuth())
            ->assertOk()->json('id');
        $this->getJson("/api/company/comment/job?job_id=$id", $this->basicAuth())->assertOk()->assertJsonCount(1, 'comments');
        $this->deleteJson("/api/company/comment/job/$commentId", [], $this->basicAuth('other'))->assertNotFound();

        $likeId = $this->postJson('/api/company/like/job', ['job_id' => $id, 'likes' => 1], $this->basicAuth())
            ->assertOk()->json('id');
        $this->deleteJson("/api/company/like/job/$likeId", [], $this->basicAuth())->assertOk();
        $this->postJson('/api/company/like/job', ['job_id' => $id, 'likes' => 1], $this->basicAuth())->assertOk();

        $this->deleteJson("/api/company/resume/$id", [], $this->basicAuth('other'))->assertNotFound();
        $this->deleteJson("/api/company/resume/$id", [], $this->basicAuth())->assertOk();
    }
}
