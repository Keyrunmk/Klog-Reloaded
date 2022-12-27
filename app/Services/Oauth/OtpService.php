<?php

namespace App\Services\Oauth;

use App\Models\User;
use Exception;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class OtpService
{
    public function getUserSecret(User $user): mixed
    {
        try {
            if($user->secret) {
                return $user->secret;
            }
    
            $mySecret = trim(Base32::encodeUpper(random_bytes(20)), '=');
            $otp = TOTP::createFromSecret($mySecret);
            $secret = $otp->getSecret();
    
            $user->secret = $secret;
            $user->save();
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }

        return $secret;
    }

    public function generateOTP(mixed $secret): mixed
    {
        try {
            $timestamp = time();
    
            $otp = TOTP::create($secret);
    
            $code = $otp->at($timestamp);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }

        return $code;
    }
}
