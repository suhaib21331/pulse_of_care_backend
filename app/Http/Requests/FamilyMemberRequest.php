<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id|unique:family_members,user_id',
            'kinship' => 'required|string|max:255',
            'elder_name' => 'required|string|max:255',
            'elder_age' => 'required|integer|min:0',
            'city' => 'required|string|max:255',
            'detailed_address' => 'required|string|max:1000',
            'notes' => 'required|string|max:1000',
        ];
    }
}
