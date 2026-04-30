<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
    public function rules()
    {
        
        $rules = [
        'service_type' => 'required|in:nurse,driver,companion',
        'service_condition' => 'required|in:normal,urgent,emergency',

        'service_address' => 'nullable|string',
        'service_latitude' => 'nullable|numeric',
        'service_longitude' => 'nullable|numeric',
        ];

        switch ($this->service_type) {

            case 'nurse':
                $rules += [
                    'nurse_major' => 'required|string',
                    
                ];
                break;

            case 'driver':
                $rules += [
                    'pickup_address' => 'required|string',
                    'pickup_latitude' => 'required|numeric',
                    'pickup_longitude' => 'required|numeric',
                    'dropoff_address' => 'required|string',
                    'dropoff_latitude' => 'required|numeric',
                    'dropoff_longitude' => 'required|numeric',
                ];
                break;

            case 'companion':
                $rules += [
                    'start_time' => 'required|date_format:H:i',
                    'end_time' => 'required|date_format:H:i',
                    'period' => 'required|in:morning,evening,full_day',
                    
                ];
                break;
        }

        return $rules;
    }
}
