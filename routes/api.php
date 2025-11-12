<?php

use App\Http\Controllers\Api\AnalyticsController;
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

Route::middleware('for-guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', fn (Request $request) => new UserResource($request->user()))->name('user.show');

    Route::controller(EmailVerificationController::class)
        ->prefix('email/verify')
        ->name('verification.')
        ->group(function () {
            Route::post('/notification', 'send')->name('send');
            Route::get('/{id}/{hash}', 'verify')->middleware('signed')->name('verify');
        });

    Route::middleware('verified')->group(function () {
        Route::controller(PostController::class)
            ->prefix('posts')
            ->name('posts.')
            ->group(function () {
                Route::get('/', 'index')->name('index')->can('viewAny', Post::class)->middleware('throttle:api');
                Route::post('/', 'store')->name('store')->can('create', Post::class);
                Route::get('/{post}', 'show')->name('show')->can('view', 'post')->middleware('throttle:api');
                Route::patch('/{post}', 'update')->name('update')->can('update', 'post');
                Route::delete('/{post}', 'destroy')->name('destroy')->can('delete', 'post');
            });

        Route::controller(CommentController::class)
            ->group(function () {
                Route::get('/posts/{post}/comments', 'index')->name('posts.comments.index')->can('viewAny', Comment::class)->middleware('throttle:api');
                Route::post('/posts/{post}/comments', 'store')->name('posts.comments.store')->can('create', Comment::class);
                Route::patch('/comments/{comment}', 'update')->name('comments.update')->can('update', 'comment');
                Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy')->can('delete', 'comment');
            });

        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])
            ->name('users.update-role')
            ->can('updateRole', 'user');

        Route::get('/meta/roles', [MetaController::class, 'roles'])
            ->name('meta.roles')
            ->middleware('throttle:api');

        Route::controller(AnalyticsController::class)
            ->prefix('analytics')
            ->name('analytics.')
            ->group(function () {
                Route::get('/posts', 'posts')->name('posts');
                Route::get('/comments', 'comments')->name('comments');
                Route::get('/users', 'users')->name('users');
            });
    });
});
