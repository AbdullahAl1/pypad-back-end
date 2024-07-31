<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getChats($userId)
    {
        $user = Auth::user();

        // Fetch chats between the authenticated user and the specified user
        $chats = Chat::where(function ($query) use ($user, $userId) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($user, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($chats);
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($chat, 201);
    }

    public function getChatUsers()
    {
        $user = Auth::user();

        // Get a list of users the authenticated user has chatted with
        $chatUsers = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with('sender', 'receiver')
            ->get()
            ->map(function ($chat) use ($user) {
                return $chat->sender_id === $user->id ? $chat->receiver : $chat->sender;
            })
            ->unique('id')
            ->values();

        return response()->json($chatUsers);
    }
}
