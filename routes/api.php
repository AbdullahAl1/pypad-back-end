<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\CodeController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);
// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('validate-token', [AuthController::class, 'validateToken']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('/chats/{userId}', [ChatController::class, 'getChats']);
    Route::post('/chats', [ChatController::class, 'sendMessage']);
    Route::get('/chat-users', [ChatController::class, 'getChatUsers']);
    Route::get('/search-users', [SearchController::class, 'search']);
    Route::post('/friend-requests', [FriendController::class, 'sendFriendRequest']);
    Route::get('/friend-requests', [FriendController::class, 'getFriendRequests']);
    Route::post('/friend-requests/{id}/accept', [FriendController::class, 'acceptFriendRequest']);
    Route::post('/friend-requests/{id}/reject', [FriendController::class, 'rejectFriendRequest']);
    Route::get('/friends', [FriendController::class, 'getFriends']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::post('add', [AdminController::class, 'addAdmin']);
        Route::post('remove', [AdminController::class, 'removeAdmin']);
        Route::get('users', [AdminController::class, 'viewUsers']);
        Route::get('admins', [AdminController::class, 'viewAdmins']);
    });
    Route::post('/codes', [CodeController::class, 'store']);
    Route::get('/codes', [CodeController::class, 'showAll']);
    Route::get('/codes/{id}', [CodeController::class, 'showOne']);
    Route::put('/codes/{id}', [CodeController::class, 'update']);
    Route::delete('/codes/{id}', [CodeController::class, 'destroy']);
});

