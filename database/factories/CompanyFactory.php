<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->employer(),
            'name' => fake()->company(),
            'industry' => fake()->randomElement(['IT', 'Savdo', 'Moliya', 'Ta\'lim', 'Ishlab chiqarish']),
            'city' => fake()->randomElement(config('ishkerakmi.cities')),
            'website' => fake()->url(),
            'email' => fake()->companyEmail(),
            'phone' => '+99871'.fake()->numerify('#######'),
            'employees_count' => fake()->numberBetween(5, 500),
            'description' => fake()->paragraph(),
        ];
    }
}
