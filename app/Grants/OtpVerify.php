<?php

namespace App\Grants;

use App\Models\User;
use App\Services\Oauth\OtpService;
use Illuminate\Support\Facades\Cache;

class OtpVerify
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    
    public function verify($otp): mixed
    {
        $user_otp = $otp;
        $hash = Cache::get('hashKey');
        $user = User::where('hash', $hash)->firstOrFail();
        $secret= $this->otpService->getUserSecret($user);
        $otp = $this->otpService->generateOTP($secret);

        if ($user_otp == $otp) {
            return $user;
        } else {
            return false;
        }
    }
}
