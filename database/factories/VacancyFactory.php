<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Enums\VacancyStatus;
use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacancy>
 */
class VacancyFactory extends Factory
{
    public function definition(): array
    {
        $from = fake()->numberBetween(3, 15) * 1_000_000;

        return [
            'company_id' => Company::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(2, true),
            'requirements' => fake()->paragraph(),
            'benefits' => fake()->sentence(),
            'city' => fake()->randomElement(config('ishkerakmi.cities')),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'experience' => fake()->randomElement(Experience::cases()),
            'salary_from' => $from,
            'salary_to' => $from + fake()->numberBetween(1, 10) * 1_000_000,
            'currency' => 'UZS',
            'skills' => fake()->randomElements(UzbekData::SKILLS, 3),
            'status' => VacancyStatus::Active,
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => VacancyStatus::Closed]);
    }
}
