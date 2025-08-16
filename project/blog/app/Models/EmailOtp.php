<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailOtp extends Model
{

    protected $fillable = ['user_id', 'otp', 'expires_at'];
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function generate($userId)
    {
        return self::create([
            'user_id' => $userId,
            'otp' => rand(100000, 999999),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
