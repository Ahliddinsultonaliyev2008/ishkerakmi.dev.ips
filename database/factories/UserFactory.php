<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(UzbekData::FIRST_NAMES).' '.fake()->randomElement(UzbekData::LAST_NAMES),
            'phone' => '+99890'.fake()->unique()->numerify('#######'),
            'email' => fake()->unique()->safeEmail(),
            'role' => UserRole::Seeker,
            'city' => fake()->randomElement(config('ishkerakmi.cities')),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }

    public function employer(): static
    {
        return $this->state(['role' => UserRole::Employer]);
    }
}
