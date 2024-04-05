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
            'user' => $this->faker->randomNumber(),
            'profession' => $this->faker->word(),
            'experience' => $this->faker->word(),
            'address' => $this->faker->address(),
            'pay' => $this->faker->randomNumber(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
