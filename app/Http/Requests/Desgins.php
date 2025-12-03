<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Desgins extends FormRequest
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
                return [
                    'en_name' => 'required',
                    'ar_name' => 'required',
                    'file'    => 'required|mimes:pdf,jpg,png,jpeg',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'en_name' => 'required',
                    'ar_name' => 'required',
                    'file'    => 'nullable|mimes:pdf,jpg,png,jpeg',
                ];

            default:
                return [];
        }
    }

    public function messages()
    {
        $lang = $this->get_lang();
        app()->setLocale($lang);
        session()->put('admin_lang', $lang);

        if ($lang === 'ar') {
            return [
                'en_name.required' => 'الاسم باللغة الإنجليزية مطلوب',
                'ar_name.required' => 'الاسم باللغة العربية مطلوب',
                'file.required'    => 'المرفق مطلوب',
                'file.mimes'       => 'يجب أن يكون امتداد الملف jpg أو png أو jpeg أو pdf',
            ];
        }

        return [
            'en_name.required' => 'English name is required',
            'ar_name.required' => 'Arabic name is required',
            'file.required'    => 'File is required',
            'file.mimes'       => 'File must be one of the following extensions: pdf, jpg, png, jpeg',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation error',
            'errors'  => $validator->errors(),
        ], 400));
    }
}
