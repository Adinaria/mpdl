<?php

namespace App\Http\Requests\API\V1\Auth;

use App\Traits\FailedValidationRequestTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserLoginRequest extends FormRequest
{
    use FailedValidationRequestTrait;

    public function rules(): array
	{
        return [
            'email'    => 'required|email',
            'password' => 'required|string|min:6|max:255',
        ];
	}

	public function authorize(): bool
	{
		return true;
	}

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors(),
                'status' => true
            ], 422)
        );
    }
}
