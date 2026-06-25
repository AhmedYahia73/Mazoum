<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomEvent extends FormRequest
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
                    'title'   => 'required',
                    'user_id' => 'required',

                    'scan_assistant_id' => 'sometimes|exists:users,id',
                    'assistant_id' => 'sometimes|exists:users,id',
                    'address' => 'required',
                    'date'    => 'required|date|date_format:Y-m-d',
                    'time'    => 'required',

                    "pdf_bottom" => ["required", "numeric"],
                    'image'   => 'sometimes',
                    'color' => ["sometimes"], 
                    "name_qr" => ["required", "boolean"],
                    "number_qr" => ["required", "boolean"],
                    "qr_height" => ["sometimes", "numeric"],
                    "qr_width" => ["sometimes", "numeric"],
                    "qr_x" => ["sometimes", "numeric"],
                    "qr_y" => ["sometimes", "numeric"],
                    "lat" => ["required", "numeric"],
                    "lng" => ["required", "numeric"],
                    "send_type" => ["required", "in:all,watts,msg"],
                    'image_height' => ['numeric'],
                    'image_width' => ['numeric'],
                    'text_color' => ['required'],
                    'show_data_pdf' => ['required', "boolean"],
                    'pdf' => ['required'],
                ];
            case 'PUT':
            case 'PATCH':
                return [
                    'title'   => 'required',
                    'user_id' => 'required',

                    "pdf_bottom" => ["required", "numeric"],
                    'scan_assistant_id' => 'sometimes|exists:users,id',
                    'assistant_id' => 'sometimes|exists:users,id',
                    'address' => 'required',
                    'date'    => 'required|date|date_format:Y-m-d',
                    'time'    => 'required',

                    'image'   => 'nullable',
                    
                    'color' => ["sometimes"], 
                    "name_qr" => ["boolean"],
                    "number_qr" => ["boolean"],
                    "qr_height" => ["numeric"],
                    "qr_width" => ["numeric"],
                    "qr_x" => ["numeric"],
                    "qr_y" => ["numeric"],
                    "lat" => ["required", "numeric"],
                    "lng" => ["required", "numeric"],
                    "send_type" => ["required", "in:all,watts,msg"],
                    'image_height' => ['numeric'],
                    'image_width' => ['numeric'],
                    'text_color' => ['required'],
                    'show_data_pdf' => ['required', "boolean"],
                    'pdf' => ['required'],
                ];

            default:
                return [];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'validation error',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
