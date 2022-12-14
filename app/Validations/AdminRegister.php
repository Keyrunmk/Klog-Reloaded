<?php

namespace App\Validations;

use Illuminate\Validation\Rule;

class AdminRegister extends Validation
{
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:255"],
            "last_name" => ["required", "string", "max:255"],
            "username" => ["required", "string", Rule::unique("admins", "username")],
            "email" => ["required", "email", Rule::unique("admins", "email")],
            "password" => ["required", "string", "min:8", "max:255"],
        ];
    }
}
