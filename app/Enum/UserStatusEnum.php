<?php

namespace App\Enum;

enum UserStatusEnum: string
{
    case Active = "active";
    case Inactive = "inactive";
    case Warned = "warned";
    case Banned = "banned";
}
