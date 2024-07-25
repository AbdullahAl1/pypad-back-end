<?php

namespace Database\Factories;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friend>
 */
class FriendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Friend::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'friend_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
