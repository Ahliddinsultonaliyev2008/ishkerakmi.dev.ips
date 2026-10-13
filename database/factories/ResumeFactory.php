<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Resume>
 */
class ResumeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->jobTitle(),
            'full_name' => fake()->randomElement(UzbekData::FIRST_NAMES).' '.fake()->randomElement(UzbekData::LAST_NAMES),
            'phone' => '+99893'.fake()->numerify('#######'),
            'email' => fake()->safeEmail(),
            'city' => fake()->randomElement(config('ishkerakmi.cities')),
            'birth_date' => fake()->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female']),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'experience' => fake()->randomElement(Experience::cases()),
            'salary_expected' => fake()->numberBetween(3, 25) * 1_000_000,
            'currency' => 'UZS',
            'skills' => fake()->randomElements(UzbekData::SKILLS, 5),
            'about' => fake()->paragraph(),
            'is_public' => true,
        ];
    }

    public function private(): static
    {
        return $this->state(['is_public' => false]);
    }
}
