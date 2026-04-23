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

    //regex:/^[0-9]{1,2}-[0-9]{1,5}$/
    /*
    "email":"m2@gmail.com",
 "password":"123446",
 "phone_number":"0780693988",
 "full_name":"mohammadddddddddddd",
 "account_type":"driver"

 "user_id":"019d5874-dd3a-7146-ae50-0e1a516899df",
 "driver_license_number":"123455",
 "car_license_number":"1234567",
 "car_color":"red",
 "car_type":"prius",
 "car_company":"toyota",
 "plate_number":"1-3456",
 "year_of_creation":"2015"
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
