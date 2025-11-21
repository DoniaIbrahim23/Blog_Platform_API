<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Posts
Route::apiResource('posts', PostController::class);

// Comments
Route::post('posts/{id}/comments', [CommentController::class, 'store'])->middleware('auth:api');
Route::get('posts/{id}/comments', [CommentController::class, 'index']);
Route::delete('comments/{id}', [CommentController::class, 'destroy'])->middleware('auth:api');
Route::put('comments/{id}', [CommentController::class, 'update'])->middleware('auth:api');
