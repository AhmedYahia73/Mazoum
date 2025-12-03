<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Assistant extends FormRequest
{
    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        if ($lang === 'en') {
            return 'en';
        }

        return 'ar';
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // اسم الجدول لازم يكون بالجمع: assistant
        $table = 'assistant';

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                return [];

            case 'POST':
                return [
                    'name'     => "required|unique:$table,name",
                    'mobile'   => "required|numeric|unique:$table,mobile",
                    'email'    => "required|email|unique:$table,email",
                    'password' => "required|min:6",
                ];

            case 'PUT':
            case 'PATCH':
                $id = $this->route('id') ?? $this->id;

                return [
                    'name'     => "required|unique:$table,name,$id",
                    'mobile'   => "required|numeric|unique:$table,mobile,$id",
                    'email'    => "required|email|unique:$table,email,$id",
                    'password' => "nullable|min:6",
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
                'name.required'     => 'الأسم مطلوب',
                'password.required' => 'كلمة المرور مطلوبة',
                'mobile.required'   => 'رقم الموبايل مطلوب',
                'mobile.numeric'    => 'رقم الموبايل يجب أن يحتوي على أرقام فقط',
                'email.required'    => 'البريد الإلكتروني مطلوب',

                'name.unique'       => 'هذا الاسم مستخدم مسبقاً',
                'password.min'      => 'كلمة المرور يجب أن تكون على الأقل 6 أحرف',
                'email.email'       => 'يجب إدخال بريد إلكتروني صالح',
                'email.unique'      => 'البريد الإلكتروني مستخدم مسبقاً',
                'mobile.unique'     => 'رقم الموبايل مستخدم مسبقاً',
            ];
        }

        return [
            'name.required'     => 'name is required',
            'password.required' => 'password is required',
            'mobile.required'   => 'mobile is required',
            'mobile.numeric'    => 'mobile must be numeric',
            'email.required'    => 'email is required',

            'name.unique'       => 'name must be unique',
            'password.min'      => 'password must be at least 6 characters',
            'email.email'       => 'email must be valid',
            'email.unique'      => 'email must be unique',
            'mobile.unique'     => 'mobile must be unique',
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
