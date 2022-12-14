<?php

namespace App\Validations;

use Illuminate\Validation\Rule;

class ValidateLoginRequest extends Validation
{
    public function rules(): array
    {
        return [
            "email" => ["required","email", Rule::exists("users","email")],
            "password" => ["required","string","min:8","max:255"],
        ];
    }
}