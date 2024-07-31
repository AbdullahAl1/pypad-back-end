<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    // Send a friend request
    public function sendFriendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $senderId = Auth::id();
        $receiverId = $request->receiver_id;

        // Check if the request already exists
        $existingRequest = FriendRequest::where('sender_id', $senderId)
                                        ->where('receiver_id', $receiverId)
                                        ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'Friend request already sent'], 400);
        }

        // Create a new friend request
        FriendRequest::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Friend request sent successfully'], 201);
    }

    // List friend requests
    public function getFriendRequests()
    {
        $userId = Auth::id();
        $friendRequests = FriendRequest::where('receiver_id', $userId)->where('status', 'pending')->with('sender')->get();

        return response()->json($friendRequests);
    }

    // Accept a friend request
    public function acceptFriendRequest($id)
    {
        $friendRequest = FriendRequest::where('id', $id)
                                        ->where('receiver_id', Auth::id())
                                        ->where('status', 'pending')
                                        ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found or already handled'], 404);
        }

        $friendRequest->update(['status' => 'accepted']);

        Friend::create([
            'user_id' => $friendRequest->sender_id,
            'friend_id' => $friendRequest->receiver_id,
        ]);

        Friend::create([
            'user_id' => $friendRequest->receiver_id,
            'friend_id' => $friendRequest->sender_id,
        ]);

        return response()->json(['message' => 'Friend request accepted'], 200);
    }

    // Reject a friend request
    public function rejectFriendRequest($id)
    {
        $friendRequest = FriendRequest::where('id', $id)
                                        ->where('receiver_id', Auth::id())
                                        ->where('status', 'pending')
                                        ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Friend request not found or already handled'], 404);
        }

        $friendRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Friend request rejected'], 200);
    }

    // List friends
    public function getFriends()
    {
        $userId = Auth::id();
        $friends = Friend::where('user_id', $userId)->with('friend')->get();

        return response()->json($friends);
    }
}
