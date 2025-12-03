<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Pricing extends FormRequest
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
                    'en_title' => 'required',
                    'ar_title' => 'required',
                    'price' => 'required',
                    'users_count' => 'required',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'en_title' => 'required',
                    'ar_title' => 'required',
                    'price' => 'required',
                    'users_count' => 'required',
                ];

            }
            default:break;
        }
    }





}
