<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Parking extends FormRequest
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

        switch($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
                {
                    return [
                        'car_code' => 'required',
                    ];
                }
            case 'PUT':
            case 'PATCH':
                {
                    return [
                        'user_name' => 'required',
                        'mobile' => 'required|numeric',
                        'car_type' => 'required',
                        'car_number' => 'required',
                        'location' => 'required',
                    ];

                }
            default:break;
        }
    }



}
