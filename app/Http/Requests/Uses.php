<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Uses extends FormRequest
{
    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        return ($lang === 'en') ? 'en' : 'ar';
    }

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
                    'en_desc' => 'required',
                    'ar_desc' => 'required',
                    'link'    => 'required',
                ];

            default:
                return [];
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation error',
            'errors'  => $validator->errors(),
        ], 400));
    }
}
