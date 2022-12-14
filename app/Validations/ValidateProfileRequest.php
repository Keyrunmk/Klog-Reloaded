<?php

namespace App\Validations;

class ValidateProfileRequest extends Validation
{
    public function rules(): array
    {
        return [
            "title" => ["nullable", "string", "max:255"],
            "description" => ["nullable", "string", "max:500"],
            "url" => ["nullable", "url", "max:255"],
        ];
    }
}