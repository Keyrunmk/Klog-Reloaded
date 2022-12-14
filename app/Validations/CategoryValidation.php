<?php

namespace App\Validations;

use Illuminate\Validation\Rule;

class CategoryValidation extends Validation
{
    public function rules(): array
    {
        return [
            "name" => ["string", "required", Rule::unique("categories", "name")],
            "slug" => ["string", "required", Rule::unique("categories", "slug")],
        ];
    }
}