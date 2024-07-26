<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Code;
use App\Models\FriendRequest;
use App\Models\Friend;
use App\Models\Chat;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
        $users = User::factory()->count(50)->create();

        // Create Codes
        Code::factory()->count(100)->create();

        // Create Friend Requests
        FriendRequest::factory()->count(50)->create();

        // Create Friends
        // Ensure user_ids and friend_ids do not duplicate
        foreach ($users as $user) {
            $friends = $users->random(rand(1, 10))->pluck('id')->toArray();
            foreach ($friends as $friendId) {
                if ($user->id !== $friendId && !Friend::where(['user_id' => $user->id, 'friend_id' => $friendId])->exists()) {
                    Friend::factory()->create([
                        'user_id' => $user->id,
                        'friend_id' => $friendId,
                    ]);
                }
            }
        }

        // Create Chats
        Chat::factory()->count(100)->create();
    }
}
