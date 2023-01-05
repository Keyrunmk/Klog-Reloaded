<?php

namespace App\Grants;

use App\Exceptions\OtpException;
use App\Models\User;
use App\Services\Oauth\OtpService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class OtpVerify
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    
    public function verify(string $hash, string $otp): bool
    {
        $user_otp = $otp;
        $hash = Cache::get("hashKey.$hash");

        if (!$hash) {
            throw new Exception("Login session expired, retry");
        }

        $user = User::where("hash", $hash)->firstOrFail();
        $secret= $user->secret;
        if (!$secret) {
            throw new Exception("TOTP secret not registered, try to enable 2FA again.");
        }
        $otp = $this->otpService->generateOTP($secret);

        if ($user_otp !== $otp) {
            throw new OtpException(
                message: "OTP not valid!",
                code: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return true;
    }
}
