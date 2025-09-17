<?php

namespace App\Http\Requests\Auth\V1;

use App\Enums\UserType;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
            'subject_id' => 'required|integer',
        ];
    }
}
