<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Events extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $method = $this->method();

        if ($method === 'GET' || $method === 'DELETE') {
            return [];
        }

        // Rules for POST
        if ($method === 'POST') {
            return [
                'country_id' => 'required|exists:countries,id',
                'scan_assistant_id' => 'sometimes|exists:users,id',
                'assistant_id' => 'sometimes|exists:users,id',
                'title' => 'required|string',
                'address' => 'required|string',
                'file' => 'nullable|mimes:pdf,jpg,png,jpeg',
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'video' => 'nullable',
                'pdf'   => 'nullable',
                'showing_qr' => 'required',
                'user_id' => 'required|exists:users,id',
                'date' => 'required|date|date_format:Y-m-d',
                'time' => 'required',
                'sending_type' => 'required|in:old_send,new_send,not_available',
                'name_qr' => 'boolean',
                'number_qr' => 'boolean',
                'qr_height' => 'numeric',
                'qr_width' => 'numeric',
                'qr_x' => 'numeric',
                'qr_y' => 'numeric',
                'resend_qr' => 'required|boolean',
                'image_height' => ['numeric'],
                'image_width' => ['numeric'],
                'text_color' => ['required'],
                'name' => ['sometimes'],
                'pdf_bottom' => ['numeric'],
                'show_data_pdf' => ['boolean'],
                'phone_setting_id' => ['required', 'exists:new_settings,id'],
            ];
        }

        // Rules for PUT/PATCH
        if ($method === 'PUT' || $method === 'PATCH') {
            return [
                'country_id' => 'required|exists:countries,id',
                'scan_assistant_id' => 'sometimes|exists:users,id',
                'assistant_id' => 'sometimes|exists:users,id',
                'title' => 'required|string',
                'address' => 'required|string',
                'file' => 'nullable|mimes:pdf,jpg,png,jpeg',
                'image' => 'nullable|mimes:jpg,png,jpeg',
                'video' => 'nullable',
                'showing_qr' => 'required',
                'user_id' => 'nullable|exists:users,id',
                'date' => 'required|date|date_format:Y-m-d',
                'time' => 'required',
                'pdf'   => 'nullable',
                'sending_type' => 'required|in:old_send,new_send,not_available',
                'name_qr' => 'required',
                'name' => ['sometimes'],
                'number_qr' => 'required',
                'qr_height' => 'required',
                'qr_width' => 'required',
                'qr_x' => 'required',
                'qr_y' => 'required',
                'resend_qr' => 'required',
                'image_height' => ['required', 'numeric'],
                'image_width' => ['required', 'numeric'],
                'text_color' => ['required'],
                'pdf_bottom' => ['numeric'],
                'show_data_pdf' => ['boolean'],
                'phone_setting_id' => ['required', 'exists:new_settings,id'],
            ];
        }

        return [];
    }

    public function messages()
    {
        // Arabic
        if (app()->getLocale() === 'ar') {
            return [
                'title.required' => 'عنوان الحدث مطلوب',
                'address.required' => 'موقع الحدث مطلوب',
                'showing_qr.required' => 'اظهار كود الـ QR مطلوب',
                'user_id.required' => 'رقم المستخدم مطلوب',
                'user_id.exists' => 'عفوا هذا المستخدم غير موجود مسبقاً',

                'file.mimes' => 'يجب أن يكون امتداد الملف pdf,jpg,png,jpeg',
            ];
        }

        // English
        return [
            'title.required' => 'Event title is required',
            'address.required' => 'Event location is required',
            'showing_qr.required' => 'Showing QR is required',
            'user_id.required' => 'User id is required',
            'user_id.exists' => 'This user does not exist',

            'file.mimes' => 'File must have extensions pdf,jpg,png,jpeg',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
