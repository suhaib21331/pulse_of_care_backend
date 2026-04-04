<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id|unique:drivers,user_id',
            'driver_license_number' => 'required|integer|unique:drivers,driver_license_number',
            'car_type' => 'required|string|max:255',
            'car_company' => 'required|string|max:255',
            'car_color' => 'required|string|max:255',
            'year_of_creation' => 'required|integer',
            'car_license_number' => 'required|integer|unique:drivers,car_license_number',
            'plate_number' => 'required|string|unique:drivers,plate_number|regex:/^[0-9]{1,2}-[0-9]{1,5}$/|max:255  ',
        ];
    }
}
