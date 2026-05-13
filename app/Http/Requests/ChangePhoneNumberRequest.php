<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePhoneNumberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|string|regex:/^07[789][0-9]{7}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.required' => 'Phone number is required',
            'phone_number.string' => 'Phone number must be a string',
            'phone_number.regex' => 'Phone number must start with 07 followed by 7,8,9 and 7 digits',
        ];
    }
}
