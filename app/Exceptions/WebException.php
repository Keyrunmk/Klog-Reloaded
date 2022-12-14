<?php

namespace App\Exceptions;

class WebException extends BaseException
{
    public function __construct(string $message, int $code)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
