<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->string('city')->index();
            $table->string('address')->nullable();
            $table->string('employment_type', 20)->index();
            $table->string('experience', 20)->index();
            $table->unsignedBigInteger('salary_from')->nullable();
            $table->unsignedBigInteger('salary_to')->nullable();
            $table->string('currency', 3)->default('UZS');
            $table->json('skills')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
