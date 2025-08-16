<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailOtpController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CommentController;

// Public OTP endpoints
Route::post('/otp/request', [EmailOtpController::class, 'sendOtp'])->name('otp.request');
Route::post('/otp/verify', [EmailOtpController::class, 'verifyOtp'])->name('otp.verify');

// Protected routes (example: get logged-in user)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    })->name('user');

    Route::get('blog-posts/search', [BlogPostController::class, 'searchPost']);
    Route::apiResource('blog-posts', BlogPostController::class);
    Route::post('/blog-posts/{postId}/comments', [CommentController::class, 'store']);
});

Route::get('/blog-posts/{postId}/comments', [CommentController::class, 'index']);
