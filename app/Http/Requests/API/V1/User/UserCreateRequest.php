<?php

namespace App\Http\Requests\API\V1\User;

use App\Traits\FailedValidationRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    use FailedValidationRequestTrait;

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'last_name' => ['required', 'string', 'max:255'],
            'password'  => ['required', 'string', 'min:6', 'max:20', 'confirmed'],
            'roles'     => ['required', 'array', 'min:1'],
            'roles.*'   => ['string', 'exists:roles,name'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
