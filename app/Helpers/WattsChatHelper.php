<?php

use App\Models\WattsChat;

if (! function_exists('log_sent_watts_message')) {

    /**
     * تسجيل رسالة واتس مرسلة من السيستم في الداتابيز
     *
     * @param string      $phone       رقم المستقبل
     * @param string      $message     نص الرسالة أو اسم الـ template
     * @param string|null $message_id  الـ message_id اللي رجع من ميتا
     * @param string|null $name        اسم المستقبل
     */
    function log_sent_watts_message(string $phone, string $message, ?string $message_id = null, ?string $name = null): void
    {
        try {
            // منع التكرار لو message_id موجود
            if ($message_id && WattsChat::where('message_id', $message_id)->exists()) {
                return;
            }

            WattsChat::create([
                'phone'         => preg_replace('/[^0-9]/', '', $phone),
                'name'          => $name,
                'message'       => $message,
                'is_sent_by_me' => true,
                'message_id'    => $message_id,
            ]);
        } catch (\Exception $e) {
            info('log_sent_watts_message error: ' . $e->getMessage());
        }
    }

}
