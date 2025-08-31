<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Traits\FailedValidationRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    use FailedValidationRequestTrait;

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6|max:20|confirmed',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
