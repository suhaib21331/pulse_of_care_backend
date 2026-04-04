<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterationRequest extends FormRequest
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
        return 
        [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'full_name' => 'required|string|min:10|max:255',
            'phone_number' => 'required|string|regex:/^07[789][0-9]{7}$/',
            'account_type' => 'required|in:nurse,companion,driver,family_member,elderly'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'full_name.required' => 'Full name is required',
            'full_name.string' => 'Full name must be a string',
            'full_name.min' => 'Full name must be at least 10 characters',
            'full_name.max' => 'Full name must not exceed 255 characters',
            'phone_number.required' => 'Phone number is required',
            'phone_number.string' => 'Phone number must be a string',
            'phone_number.regex' => 'Phone number must start with 07 followed by 7,8,9 and 7 digits',
            'account_type.required' => 'Account type is required'
        ];
    }
}
