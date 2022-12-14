<?php

namespace App\Validations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class Validation
{
    abstract public function rules(): array;

    public function validate(Request $request): array
    {
        $request =  $request->all();

        return Validator::make($request, $this->rules())->validate();
    }
}