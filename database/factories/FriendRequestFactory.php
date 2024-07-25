<?php

namespace Database\Factories;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FriendRequest>
 */
class FriendRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = FriendRequest::class;


    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
