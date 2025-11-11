<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Models\Post;
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
    Route::controller(EmailVerificationController::class)->group(function () {
        Route::post('/email/verification-notification', 'send')->name('verification.send');
        Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
    });

    Route::middleware('verified')->group(function () {
        Route::controller(PostController::class)->group(function () {
            Route::get('/posts', 'index')->name('posts.index')->can('viewAny', Post::class);
            Route::post('/posts', 'store')->name('posts.store')->can('create', Post::class);
            Route::get('/posts/{post}', 'show')->name('posts.show')->can('view', 'post');
            Route::patch('/posts/{post}', 'update')->name('posts.update')->can('update', 'post');
            Route::delete('/posts/{post}', 'destroy')->name('posts.destroy')->can('delete', 'post');
        });

        Route::controller(CommentController::class)->group(function () {
            Route::get('/posts/{post}/comments', 'index')->name('posts.comments.index')->can('viewAny', Comment::class);
            Route::post('/posts/{post}/comments', 'store')->name('posts.comments.store')->can('create', Comment::class);
            Route::patch('/comments/{comment}', 'update')->name('comments.update')->can('update', 'comment');
            Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy')->can('delete', 'comment');
        });

        Route::prefix('meta')->group(function () {
            Route::get('/roles', [MetaController::class, 'roles'])->name('roles.index');
        });

        Route::get('/user', fn (Request $request) => new UserResource($request->user()));
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role')->can('updateRole', 'user');
    });
});
