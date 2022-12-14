<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class NotFoundException extends BaseException
{
    public function __construct(string $message = "Could'nt find what you were looking for.", int $code = Response::HTTP_NOT_FOUND)
    {
        $this->message = $message;
        $this->code = $code;
    }
}