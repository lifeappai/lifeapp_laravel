<?php

namespace App\Http\Requests\Auth\V1;

use App\Enums\UserType;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'school_id'  => 'integer',
			'name'       => 'required',
			'student_id' => 'integer',
			'dob'        => 'required',
			'gender'     => 'required|integer',
			'grade'      => 'required|integer',
			'address'    => 'required',
			'mobile_no'  => 'required|numeric',
			'state'      => 'required',
			'city'       => 'required',
		];
	}
}
