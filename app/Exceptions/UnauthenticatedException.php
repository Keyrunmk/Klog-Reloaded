<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnauthenticatedException extends BaseException
{
    public function __construct(string $message = "You need to login", int $code = Response::HTTP_PROXY_AUTHENTICATION_REQUIRED)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
