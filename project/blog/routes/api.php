<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailOtpController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CommentController;

// Public OTP endpoints
Route::post('otp/request', [EmailOtpController::class, 'sendOtp']);
Route::post('otp/verify', [EmailOtpController::class, 'verifyOtp']);
// Public register api
Route::post('register', [EmailOtpController::class, 'register']);


// Public comments listing for a blog post
Route::get('blog-posts/{post}/comments', [CommentController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('blog-posts', [BlogPostController::class, 'store']);
    Route::put('blog-posts/{blogPost}', [BlogPostController::class, 'update']);
    Route::delete('blog-posts/{blogPost}', [BlogPostController::class, 'destroy']);

    // Add comment to a blog post
    Route::post('blog-posts/{post}/comments', [CommentController::class, 'store']);
    Route::post('logout', [EmailOtpController::class, 'logout']);
});

// public Blog posts routes
Route::get('blog-posts/search', [BlogPostController::class, 'searchPost']);
Route::get('blog-posts', [BlogPostController::class, 'index']);
Route::get('blog-posts/{blogPost}', [BlogPostController::class, 'show']);
