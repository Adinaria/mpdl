<?php

namespace App\Http\Requests\API\V1\User;

use App\Traits\FailedValidationRequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    use FailedValidationRequestTrait;

    public function rules(): array
    {
        $presenceRule = $this->isMethod('patch') ? 'sometimes' : 'required';

        return [
            'name'      => [$presenceRule, 'string', 'max:255'],
            'email'     => [
                $presenceRule,
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('uuid'), 'uuid'),
            ],
            'last_name' => [$presenceRule, 'string', 'max:255'],
            'password'  => [$presenceRule, 'string', 'min:6', 'max:20', 'confirmed'],
            'roles'     => [$presenceRule, 'array', 'min:1'],
            'roles.*'   => ['string', 'exists:roles,name'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
