<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class Currency extends FormRequest
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
                    'en_name' => 'required',
                    'ar_name' => 'required',
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
            ];
        }

        return [
            'en_name.required' => 'English name is required',
            'ar_name.required' => 'Arabic name is required',
        ];
    }


    // public function messages()
    // {

    //     $lang = $this->get_lang();

    //     if($lang == null) {
    //         $lang = 'ar';app()->setLocale('ar');session()->put('admin_lang','ar');
    //     }

    //     if($lang == 'ar') {

    //         // use trans instead on Lang
    //         return [
    //             'en_name.required' => ' الأسم باللغة الانجليزية مطلوب ',
    //             'code.required' => 'الأسم باللغة بالعربية مطلوب',

    //             'users_count.required' =>  'عدد المستخدمين مطلوب',
    //             'users_count.numeric' =>  'عدد المستخدمين يجب ان يحتوي علي ارقام',
    //             'users_count.min' =>  'عدد المستخدمين يجب ان يحتوي علي الاقل 1',

    //             'price.required' =>  'السعر مطلوب',
    //             'price.numeric' =>  'السعر يجب ان يحتوي علي ارقام',
    //             'price.min' =>  'السعر يجب ان يحتوي علي الاقل 1',


    //         ];

    //     } else {

    //         return [
    //             'en_name.required' => ' en name required ',
    //             'code.required' => ' ar name required ',

    //             'users_count.required' =>  'users count is required',
    //             'users_count.numeric' =>  'users count must be numeric',
    //             'users_count.min' =>  'users count must be at least one',

    //             'price.required' =>  'price is required',
    //             'price.numeric' =>  'price must be numeric',
    //             'price.min' =>  'price must be at least one',

    //         ];
    //     }

    // }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation error',
            'errors'  => $validator->errors(),
        ], 400));
    }
}
