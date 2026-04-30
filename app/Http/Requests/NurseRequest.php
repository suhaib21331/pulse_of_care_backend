<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NurseRequest extends FormRequest
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
            'major' => 'required|string|max:255',
            'years_of_experience' => 'required|string|max:255',
            'license_number' => 'required|min:5|max:7|unique:nurses,license_number',
            'work_place' => 'required|string|max:255',
            'about_you' => 'required|string|max:1000',
        ];
    }
}
