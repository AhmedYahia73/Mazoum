<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Users extends FormRequest
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
                    'name'        => 'required',
                    'mobile'      => 'required|numeric|unique:users',
                    'mobile_code' => 'required',
                ];

            case 'PUT':
            case 'PATCH':
                $id = $this->get('id');
                return [
                    'name'        => 'required',
                    'mobile'      => "required|numeric|unique:users,mobile,$id",
                    'mobile_code' => 'required',
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
                'name.required'         => 'الأسم مطلوب',
                'name.unique'           => 'الأسم لا يجب أن يحتوي على قيم موجودة مسبقاً',

                'mobile_code.required'  => 'كود الموبايل مطلوب',

                'mobile.required'       => 'رقم الموبايل مطلوب',
                'mobile.numeric'        => 'رقم الموبايل يجب أن يحتوي على أرقام فقط',
                'mobile.unique'         => 'رقم الموبايل مستخدم مسبقاً',

                'email.required'        => 'البريد الإلكتروني مطلوب',
                'email.email'           => 'يجب إدخال بريد إلكتروني صحيح',
                'email.unique'          => 'البريد الإلكتروني مستخدم مسبقاً',

                'password.required'     => 'كلمة المرور مطلوبة',
                'password.min'          => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            ];
        }

        return [
            'name.required'        => 'name is required',
            'name.unique'          => 'name must be unique',

            'mobile_code.required' => 'mobile code is required',

            'mobile.required'      => 'mobile is required',
            'mobile.numeric'       => 'mobile must be numeric',
            'mobile.unique'        => 'mobile must be unique',

            'email.required'       => 'email is required',
            'email.email'          => 'email must be a valid email',
            'email.unique'         => 'email must be unique',

            'password.required'    => 'password is required',
            'password.min'         => 'min password is 6 characters',
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
