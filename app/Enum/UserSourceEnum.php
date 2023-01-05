<?php

namespace App\Enum;

enum UserSourceEnum: string
{
    case Local = "local";
    case Foreign = "foreign";
}
