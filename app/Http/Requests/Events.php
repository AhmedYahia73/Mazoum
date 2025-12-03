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
                'country_code' => 'required',
                'title' => 'required|string',
                'address' => 'required|string',
                'file' => 'required|mimes:pdf,jpg,png,jpeg',
                'showing_qr' => 'required',
                'user_id' => 'required|exists:users,id',
                'date' => 'required|date|date_format:Y-m-d',
                'time' => 'required',
                'enable_resend_again' => 'required|in:yes,no',
                'sending_type' => 'required|in:old_send,new_send,not_available',
                'video' => 'nullable',
            ];
        }

        // Rules for PUT/PATCH
        if ($method === 'PUT' || $method === 'PATCH') {
            return [
                'country_code' => 'required',
                'title' => 'required|string',
                'address' => 'required|string',
                'file' => 'nullable|mimes:pdf,jpg,png,jpeg',
                'showing_qr' => 'required',
                'user_id' => 'nullable|exists:users,id',
                'date' => 'required|date|date_format:Y-m-d',
                'time' => 'required',
                'enable_resend_again' => 'required|in:yes,no',
                'sending_type' => 'required|in:old_send,new_send,not_available',
                'video' => 'nullable',
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

                'file.required' => 'المرفق مطلوب',
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

            'file.required' => 'File is required',
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
