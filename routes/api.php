<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-email', [AuthController::class, 'verifyEmail']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('/chats/{userId}', [ChatController::class, 'getChats']);
    Route::post('/chats', [ChatController::class, 'sendMessage']);
    Route::get('/chat-users', [ChatController::class, 'getChatUsers']);
    Route::get('/search-users', [SearchController::class, 'search']);
    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::post('add', [AdminController::class, 'addAdmin']);
        Route::post('remove', [AdminController::class, 'removeAdmin']);
        Route::get('users', [AdminController::class, 'viewUsers']);
        Route::get('admins', [AdminController::class, 'viewAdmins']);
    });
});
