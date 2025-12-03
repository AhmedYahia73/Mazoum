<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomEvent extends FormRequest
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
                    'title'   => 'required',
                    'user_id' => 'required',

                    'address' => 'required',
                    'date'    => 'required|date|date_format:Y-m-d',
                    'time'    => 'required',

                    'image'   => 'required|image',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'title'   => 'required',
                    'user_id' => 'required',

                    'address' => 'required',
                    'date'    => 'required|date|date_format:Y-m-d',
                    'time'    => 'required',

                    'image'   => 'nullable|image',
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
