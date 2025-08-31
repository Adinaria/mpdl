<?php

namespace App\Http\Requests\API\V1\Role;

use App\Traits\FailedValidationRequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
	use FailedValidationRequestTrait;

	public function rules(): array
	{
		return [
			'name' => [
				'required',
				'string',
				'max:255',
				Rule::unique('roles', 'name')->ignore($this->route('uuid'), 'uuid'),
			],
		];
	}

	public function authorize(): bool
	{
		return true;
	}
}
