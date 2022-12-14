<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ForbiddenException extends BaseException
{
    public function __construct(string $message = "This page is forbidden", int $code = Response::HTTP_FORBIDDEN)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
