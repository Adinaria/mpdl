<?php

namespace App\Http\Requests\API\V1\Role;

use App\Traits\FailedValidationRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class RoleCreateRequest extends FormRequest
{
    use FailedValidationRequestTrait;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
