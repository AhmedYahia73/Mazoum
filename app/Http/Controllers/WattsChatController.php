<?php

namespace App\Http\Controllers;

use App\Events\WattsChat as WattsChatEvent;
use App\Http\Controllers\Controller;
use App\Models\WattsChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class WattsChatController extends Controller
{ 
    // 1. دالة التحقق (Verification) المطلوبة من ميتا
    public function verifyWebhook(Request $request)
    {
        // التوكن ده انت اللي بتكتبه في لوحة تحكم ميتا، وممكن تحفظه في ملف .env
        $verify_token = env('WHATSAPP_VERIFY_TOKEN', 'your_custom_secure_token'); 

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verify_token) {
                // لازم ترجع الـ challenge زي ما هو عشان ميتا تقبل الرابط
                return response($challenge, 200);
            }
            return response()->json(['error' => 'Invalid token'], 403);
        }
        return response()->json(['error' => 'Bad request'], 400);
    }

    // WHATSAPP_PHONE_NUMBER_ID, MY_OFFICIAL_PHONE_NUMBER, WHATSAPP_ACCESS_TOKEN
    public function receiveMessage(Request $request) {
        $payload = $request->all();

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'])) {
            $value = $payload['entry'][0]['changes'][0]['value'];
            $messageData = $value['messages'][0];
            $messageId = $messageData['id'];

            // 1. التأكد إن الرسالة دي مصلتش قبل كدة (عشان نمنع التكرار مع sendMessage)
            $alreadyExists = WattsChat::where('message_id', $messageId)->exists();
            if ($alreadyExists) {
                return response()->json(['status' => 'already_processed'], 200);
            }

            $my_phone = env('MY_OFFICIAL_PHONE_NUMBER');
            $isSentByMe = (isset($messageData['from']) && $messageData['from'] == $my_phone);

            // تنظيف رقم التليفون
            $customerPhone = preg_replace('/[^0-9]/', '', ($isSentByMe ? $messageData['to'] : $messageData['from']));

            $message = WattsChat::create([
                'phone' => $customerPhone,
                'message' => $messageData['text']['body'] ?? '',
                'is_sent_by_me' => $isSentByMe,
                'message_id' => $messageId,
                'my_phone' => $messageData['from'],
            ]);

            WattsChatEvent::dispatch($message);
        }
        return response()->json(['status' => 'success'], 200);
    }
}
