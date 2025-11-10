<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware('guest.api')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));

    Route::controller(EmailVerificationController::class)->group(function () {
        Route::post('/email/verification-notification', 'send')->name('verification.send');
        Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
    });

    Route::middleware('verified')->group(function () {
        Route::apiResource('posts', PostController::class);

        Route::controller(CommentController::class)->group(function () {
            Route::get('/posts/{post}/comments', 'index')->name('posts.comments.index');
            Route::post('/posts/{post}/comments', 'store')->name('posts.comments.store');
        });
        Route::apiResource('comments', CommentController::class)->only(['update', 'destroy']);
    });
});
