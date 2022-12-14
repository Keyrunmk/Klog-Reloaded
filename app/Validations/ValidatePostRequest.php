<?php

namespace App\Validations;

use Illuminate\Validation\Rule;

class ValidatePostRequest extends Validation
{
    public function rules(): array
    {
        return [
            "slug" => ["required", "string", "max:255"],
            "title" => ["required", "string", "max:255"],
            "body" => ["required", "string", "max:255"],
            "category_id" => ["required", "string", Rule::exists("categories", "id")],
            "image" => ["nullable", "image"],
        ];
    }
}
