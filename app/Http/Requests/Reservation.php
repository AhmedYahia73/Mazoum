<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Reservation extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {

            case 'GET':
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'event_name'      => 'required',
                    'event_date'      => 'required|date|date_format:Y-m-d',
                    'event_address'   => 'required',
                    'events_count'    => 'required',
                    'package_price'   => 'required',
                    'mobile'          => 'required|numeric|min:1',
                    'gender'          => 'required',
                    'employees_count' => 'required',
                    'employee_name'   => 'required',
                    'office_name'     => 'required',
                    'image'           => 'required',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'event_name'      => 'required',
                    'event_date'      => 'required|date|date_format:Y-m-d',
                    'event_address'   => 'required',
                    'events_count'    => 'required',
                    'package_price'   => 'required',
                    'mobile'          => 'required|numeric|min:1',
                    'gender'          => 'required',
                    'employees_count' => 'required',
                    'employee_name'   => 'required',
                    'office_name'     => 'required',
                    'image'           => 'nullable',
                ];

            default:
                return [];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'validation error',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
