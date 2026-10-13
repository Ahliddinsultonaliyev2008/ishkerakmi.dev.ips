<?php

namespace Database\Factories;

use App\Models\Jobs;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class JobsFactory extends Factory
{
    protected $model = Jobs::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->jobTitle(),
            'profession' => $this->faker->word(),
            'type' => $this->faker->word(),
            'address' => $this->faker->address(),
            'pay' => $this->faker->randomNumber(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'experience' => $this->faker->word(),
            'language' => $this->faker->languageCode(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
