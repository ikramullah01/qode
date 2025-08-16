<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::firstOrCreate(['email' => $request->email]);


        $otp = rand(100000, 999999);
        EmailOtp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw("Your login OTP is: {$otp}", function ($message) use ($user) {
            $message->to($user->email)->subject('Your Login OTP');
        });

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $otpRecord = EmailOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->latest()
            ->first();

        if (! $otpRecord || $otpRecord->expires_at->isPast()) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        $otpRecord->delete();

        // Create Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete(); // for Sanctum token
        return response()->json(['message' => 'Logged out successfully']);
    }
}
