<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WattsChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AdminWattsChatController extends Controller
{

    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'from' => 'required|sa,kw',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  

        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $chatHistory = WattsChat::
        where('phone', $phone) 
        where('from', $request->from) 
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $chatHistory
        ], 200);
    }
    
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'message' => 'required',
        ]); 
        if ($validator->fails()) { // if Validate Make Error Return Message Error
            return response()->json([
                'errors' => $validator->errors(),
            ],400);
        }  
        // 1. استلام الداتا من واجهة الشات بتاعتك
        $recipientPhone = $request->input('phone'); // رقم العميل (لازم بكود الدولة من غير + مثلا: 201xxxxxxxxx)
        $messageText = $request->input('message'); // محتوى الرسالة

        // 2. سحب بيانات ميتا من ملف الـ .env
        $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $my_phone = env('MY_OFFICIAL_PHONE_NUMBER');
        $accessToken = env('WHATSAPP_ACCESS_TOKEN');

        // 3. إرسال الطلب لـ Meta Cloud API
        $response = Http::withToken($accessToken)
            ->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $recipientPhone,
                'type' => 'text',
                'text' => [
                    'preview_url' => true, // عشان لو باعت لينك يظهرله Preview
                    'body' => $messageText
                ]
            ]);

        // 4. التعامل مع الرد من ميتا
        if ($response->successful()) {
            $metaResponse = $response->json();
            $messageId = $metaResponse['messages'][0]['id'] ?? null; // الـ ID بتاع الرسالة المرسلة

            $message = WattsChat::create([
                'phone' => $recipientPhone,
                'message' => $messageText,
                'is_sent_by_me' => true, // هنا هيتحفظ كـ outgoing لو إنت اللي بعته من الواتساب
                'message_id' => $messageId,
                "my_phone" => $my_phone,
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'تم إرسال الرسالة بنجاح',
                'message_id' => $messageId
            ]);
        }

        // في حالة حدوث خطأ (مثلاً الرقم غلط أو التوكن منتهي)
        return response()->json([
            'status' => 'error', 
            'error' => $response->json()
        ], $response->status());
    }
}
