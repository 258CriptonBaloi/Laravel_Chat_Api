<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('sign-in', [AuthController::class, 'login']);
    Route::post('sign-up', [AuthController::class, 'register']);
    Route::post('sign-out', [AuthController::class, 'logout']);
    Route::post('validate-token', [AuthController::class, 'validateToken']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

//Rotas do chat
Route::middleware('auth:api')->group(function () {
    Route::get('chat/list-users', [ChatsController::class, 'listUsers']);
    Route::get('chat/list-chat', [ChatsController::class, 'listChatsWithLastMessages']);
    Route::post('chat/start-chat', [ChatsController::class, 'startChat']);
    Route::post('chat/message', [ChatsController::class, 'sendMessage']);
    Route::get('chat/{chat_id}/messages', [ChatsController::class, 'listMessage']);
});