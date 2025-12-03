<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MobileCodes extends FormRequest
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
            case 'PUT':
            case 'PATCH':
                return [
                    'en_country_name' => 'required',
                    'ar_country_name' => 'required',
                    'country_code' => 'required',
                    'code' => 'required',
                ];

            default:
                return [];
        }
    }

    /*
    public function messages()
    {
        $lang = app()->getLocale();

        if ($lang === 'ar') {
            return [
                'en_country_name.required' => ' الأسم باللغة الانجليزية مطلوب ',
                'ar_country_name.required' => ' الأسم باللغة بالعربية مطلوب ',
                'country_code.required' => 'كود الدولة مطلوب',
                'code.required' => 'الكود مطلوب',
            ];
        }

        return [
            'en_country_name.required' => 'English country name is required',
            'ar_country_name.required' => 'Arabic country name is required',
            'country_code.required' => 'Country code is required',
            'code.required' => 'Code is required',
        ];
    }
    */

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
