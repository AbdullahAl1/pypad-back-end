<?php

namespace Database\Factories;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'message' => fake()->text(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
