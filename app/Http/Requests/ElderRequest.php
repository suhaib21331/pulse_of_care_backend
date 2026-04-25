<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ElderRequest extends FormRequest
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
                'age' => 'required|integer|min:50|max:100',
                'gender' => 'required|string|in:male,female',
                'chronic_diseases' => 'required|string|max:1000',
                'current_medications' => 'required|string|max:1000',
                'allergies' => 'required|string|max:1000',
                'uses_diapers' => 'required|boolean',
                'movement_level' => 'required|string|max:255',
                'need_wheel_chair' => 'required|boolean',
                'city' => 'required|string|max:255',
                'detailed_address' => 'required|string|max:1000',
                'notes' => 'required|string|max:1000'
        ];
    }
}
