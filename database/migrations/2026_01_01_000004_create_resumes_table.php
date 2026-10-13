<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('full_name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('city')->index();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('employment_type', 20)->index();
            $table->string('experience', 20)->index();
            $table->unsignedBigInteger('salary_expected')->nullable();
            $table->string('currency', 3)->default('UZS');
            $table->json('skills')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_public')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resumes');
    }
};
