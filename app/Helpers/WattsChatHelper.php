<?php

use App\Models\WattsChat;
use App\Models\Setting;

if (! function_exists('log_sent_watts_message')) {

    /**
     * تسجيل رسالة واتس مرسلة من السيستم في الداتابيز
     *
     * @param string      $phone          رقم المستقبل
     * @param string      $template_name  اسم الـ template
     * @param string|null $message_id     الـ message_id اللي رجع من ميتا
     * @param string|null $name           اسم المستقبل
     * @param string|null $phone_numer_id الـ phone_number_id اللي بعتت منه
     */
    function log_sent_watts_message(
        string $phone,
        string $template_name,
        ?string $message_id = null,
        ?string $name = null,
        ?string $phone_numer_id = null
    ): void {
        try {
            if ($message_id && WattsChat::where('message_id', $message_id)->exists()) {
                return;
            }

            $from = 'kw';
            if ($phone_numer_id) {
                $setting = Setting::first();
                if ($setting && $setting->sa_phone_numer_id && $phone_numer_id == $setting->sa_phone_numer_id) {
                    $from = 'sa';
                }
            }

            WattsChat::create([
                'phone'         => preg_replace('/[^0-9]/', '', $phone),
                'name'          => $name,
                'message'       => $template_name,
                'template_name' => $template_name,
                'is_sent_by_me' => true,
                'message_id'    => $message_id,
                'from'          => $from,
            ]);
        } catch (\Exception $e) {
            info('log_sent_watts_message error: ' . $e->getMessage());
        }
    }

}
