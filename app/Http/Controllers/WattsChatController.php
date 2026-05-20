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
        $data = $request->all();

        info('WattsChat webhook received', $data);

        // statuses (sent/delivered/read) — نتجاهلها
        if (isset($data['entry'][0]['changes'][0]['value']['statuses'])) {
            return response()->json(['status' => 'ok'], 200);
        }

        // رسائل واردة
        if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
            $value       = $data['entry'][0]['changes'][0]['value'];
            $messageData = $value['messages'][0];
            $messageId   = $messageData['id'];
            $type        = $messageData['type'] ?? 'text';

            // منع التكرار
            if (WattsChat::where('message_id', $messageId)->exists()) {
                return response()->json(['status' => 'already_processed'], 200);
            }

            $my_phone    = env('MY_OFFICIAL_PHONE_NUMBER');
            $displayPhone = $value['metadata']['display_phone_number'] ?? $my_phone;

            // الرسالة الواردة دايماً من الـ customer
            $customerPhone = preg_replace('/[^0-9]/', '', $messageData['from']);

            // استخراج نص الرسالة حسب النوع
            $messageText = match($type) {
                'text'        => $messageData['text']['body']                    ?? '',
                'button'      => $messageData['button']['text']                  ?? '',
                'interactive' => $messageData['interactive']['nfm_reply']['body'] ?? 
                                 $messageData['interactive']['button_reply']['title'] ?? '',
                default       => '[' . $type . ']',
            };

            // اسم المرسل لو موجود
            $senderName = $value['contacts'][0]['profile']['name'] ?? null;

            $message = WattsChat::create([
                'phone'        => $customerPhone,
                'name'         => $senderName,
                'message'      => $messageText,
                'is_sent_by_me'=> false,
                'message_id'   => $messageId,
            ]);

            WattsChatEvent::dispatch($message);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
