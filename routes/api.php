<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\CodeController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);

// Protected routes
Route::middleware('auth:api')->group(function () {

    // Auth routes
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('validate-token', [AuthController::class, 'validateToken']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Chat routes
    Route::get('/chats/{userId}', [ChatController::class, 'getChats']);
    Route::post('/chats', [ChatController::class, 'sendMessage']);
    Route::get('/chat-users', [ChatController::class, 'getChatUsers']);

    // Search route
    Route::get('/search-users', [SearchController::class, 'search']);

    // Friend request routes
    Route::prefix('friend-requests')->group(function () {
        Route::post('/', [FriendController::class, 'sendFriendRequest']);
        Route::get('/', [FriendController::class, 'getFriendRequests']);
        Route::post('/{id}/accept', [FriendController::class, 'acceptFriendRequest']);
        Route::post('/{id}/reject', [FriendController::class, 'rejectFriendRequest']);
    });
    Route::get('/friends', [FriendController::class, 'getFriends']);

    // Code routes
    Route::prefix('codes')->group(function () {
        Route::post('/', [CodeController::class, 'store']);
        Route::get('/', [CodeController::class, 'showAll']);
        Route::get('/{id}', [CodeController::class, 'showOne']);
        Route::put('/{id}', [CodeController::class, 'update']);
        Route::delete('/{id}', [CodeController::class, 'destroy']);
    });

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::post('add', [AdminController::class, 'addAdmin']);
        Route::post('remove', [AdminController::class, 'removeAdmin']);
        Route::get('users', [AdminController::class, 'viewUsers']);
        Route::get('admins', [AdminController::class, 'viewAdmins']);
        Route::post('user', [AdminController::class, 'addUser']);
    });
});
