<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class Admin extends FormRequest
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
                    'name'     => 'required|unique:admin,name',
                    'mobile'   => 'required|numeric|unique:admin,mobile',
                    'email'    => 'required|email|unique:admin,email',
                    'password' => 'required|min:6',
                ];

            case 'PUT':
            case 'PATCH':
                return [
                    'name'     => ['required', Rule::unique('admin', 'name')->ignore($this->id)],
                    'mobile'   => ['required', 'numeric', Rule::unique('admin', 'mobile')->ignore($this->id)],
                    'email'    => ['required', 'email', Rule::unique('admin', 'email')->ignore($this->id)],
                    'password' => 'nullable',
                ];
        }
    }

    public function messages()
    {
        $lang = session('admin_lang', 'ar'); // safer default

        if ($lang === 'ar') {
            return [
                'name.required'     => 'الأسم مطلوب',
                'password.required' => 'كلمة المرور مطلوب',
                'mobile.required'   => 'رقم الموبيل مطلوب',
                'mobile.numeric'    => 'رقم الموبيل يجب ان يحتوي علي ارقام فقط',
                'email.required'    => 'البريد الألكتروني مطلوب',

                'name.unique'       => 'الأسم لا يجب اي يحتوي علي قيم موجوده مسبقا',
                'password.min'      => 'كلمه المرور لابد ان تحتوي علي الاقل ٦ ارقام',
                'email.email'       => 'يجب ان يحتوي البريد الألكتروني علي بريد الكتروني',
                'email.unique'      => 'البريد الألكتروني موجود مسبقا',
                'mobile.unique'     => 'رقم الموبايل موجود مسبقا',
            ];
        }

        return [
            'name.required'     => 'name is required',
            'password.required' => 'password is required',
            'mobile.required'   => 'mobile is required',
            'mobile.numeric'    => 'mobile must be numeric',
            'email.required'    => 'email is required',

            'name.unique'       => 'name must be unique',
            'password.min'      => 'min password is 6 characters',
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
