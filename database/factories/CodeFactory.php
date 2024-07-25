<?php

namespace Database\Factories;
use App\Models\Code;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Code>
 */
class CodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Code::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => fake()->text(),
            'filename' => fake()->word() . '.py',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
