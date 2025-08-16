<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailOtpController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CommentController;

// Public OTP endpoints
Route::post('otp/request', [EmailOtpController::class, 'sendOtp']);
Route::post('otp/verify', [EmailOtpController::class, 'verifyOtp']);

// Public comments listing for a blog post
Route::get('blog-posts/{post}/comments', [CommentController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Get logged-in user
    Route::get('user', fn (Request $request) => response()->json($request->user()))
        ->name('user');

    // Blog posts
    Route::get('blog-posts/search', [BlogPostController::class, 'searchPost']);
    Route::apiResource('blog-posts', BlogPostController::class);

    // Add comment to a blog post
    Route::post('blog-posts/{post}/comments', [CommentController::class, 'store']);
});
