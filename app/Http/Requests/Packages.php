<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Packages extends FormRequest
{

    public function get_lang()
    {
        $lang = session()->get('admin_lang');

        if($lang == 'en' && $lang != null) {
            return $lang;
        } else {
            return 'ar';
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'en_name' => 'required',
                    'ar_name' => 'required',
                    'users_count' => 'required|numeric|min:1',
                    'price' => 'required',
                    'currency_id' => 'required',
                  	'image' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'en_name' => 'required',
                    'ar_name' => 'required',
                    'users_count' => 'required|numeric|min:1',
                    'price' => 'required',
                    'currency_id' => 'required',
                  	'image' => 'nullable',

                ];

            }
            default:break;
        }
    }

    public function messages()
    {

        $lang = $this->get_lang();

        if($lang == null) {
            $lang = 'ar';app()->setLocale('ar');session()->put('admin_lang','ar');
        }

        if($lang == 'ar') {

            // use trans instead on Lang
            return [
                'en_name.required' => ' الأسم باللغة الانجليزية مطلوب ',
                'ar_name.required' => 'الأسم باللغة بالعربية مطلوب',

                'users_count.required' =>  'عدد المستخدمين مطلوب',
                'users_count.numeric' =>  'عدد المستخدمين يجب ان يحتوي علي ارقام',
                'users_count.min' =>  'عدد المستخدمين يجب ان يحتوي علي الاقل 1',

                'price.required' =>  'السعر مطلوب',
                'price.numeric' =>  'السعر يجب ان يحتوي علي ارقام',
                'price.min' =>  'السعر يجب ان يحتوي علي الاقل 1',


            ];

        } else {

            return [
                'en_name.required' => ' en name required ',
                'ar_name.required' => ' ar name required ',

                'users_count.required' =>  'users count is required',
                'users_count.numeric' =>  'users count must be numeric',
                'users_count.min' =>  'users count must be at least one',

                'price.required' =>  'price is required',
                'price.numeric' =>  'price must be numeric',
                'price.min' =>  'price must be at least one',

            ];
        }

    }






}
