<?php

namespace App\Validations;

use Illuminate\Validation\Rule;

class AdminLogin extends Validation
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email", Rule::exists("admins", "email")],
            "password" => ["required", "string", "min:8", "max:255"]
        ];
    }
}
