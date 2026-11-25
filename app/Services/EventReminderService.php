<?php

namespace App\Services;

use App\Models\Events;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Setting;
use App\Models\WattsChat as WattsChatModel;
use App\Services\WhatsApp\WhatsAppMediaOptimizerService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EventReminderService
{
    /**
     * أسماء أيام الأسبوع بالعربية
     */
    private const ARABIC_DAYS = [
        'Sunday'    => 'الأحد',
        'Monday'    => 'الاثنين',
        'Tuesday'   => 'الثلاثاء',
        'Wednesday' => 'الأربعاء',
        'Thursday'  => 'الخميس',
        'Friday'    => 'الجمعة',
        'Saturday'  => 'السبت',
    ];

    /**
     * Build the query for event users eligible to receive a reminder
     * who have not yet been confirmed as reminded (remember is null or 0).
     *
     * @param  int     $eventId
     * @param  string  $target  'all' | 'confirmed' | 'unconfirmed'
     * @return Builder
     */
    public static function eligibleForReminderQuery(int $eventId, string $target = 'all'): Builder
    {
        $query = EventUsers::where('event_id', $eventId)
            ->where(function ($q) {
                $q->whereNull('remember')->orWhere('remember', 0);
            });

        if ($target === 'confirmed') {
            return $query->where('accept_count', '>', 0);
        }

        if ($target === 'unconfirmed') {
            return $query->where('accept_count', 0)
                ->where('status', '!=', 'not-attend')
                ->where(function ($sub) {
                    $sub->where('is_new_sent', '!=', 0)
                        ->orWhere('status', '!=', 'hold')
                        ->orWhereNotNull('is_sent');
                });
        }

        // 'all': يشمل الطرفين (المؤكدين accept_count > 0 أو غير المؤكدين الذين أُرسلت لهم الدعوة ولم يعتذروا)
        return $query->where(function ($q) {
            $q->where('accept_count', '>', 0)
                ->orWhere(function ($sub) {
                    $sub->where('accept_count', 0)
                        ->where('status', '!=', 'not-attend')
                        ->where(function ($inner) {
                            $inner->where('is_new_sent', '!=', 0)
                                ->orWhere('status', '!=', 'hold')
                                ->orWhereNotNull('is_sent');
                        });
                });
        });
    }

    /**
     * Send reminder messages for a given event and collection of EventUsers.
     * For Meta Cloud API, remember is NOT set to 1 on HTTP 200; it is confirmed
     * asynchronously by the Meta Webhook upon actual delivery (delivered/read).
     *
     * @param  Events         $event
     * @param  Collection     $eventUsers
     * @param  int            $delayPerMsg
     * @param  int            $batchDelay
     * @param  string         $logPrefix
     * @param  callable|null  $outputCallback  function(string $level, string $message): void
     * @return array{processed: int, accepted: int, failed: int}
     */
    public static function sendRemindersForEvent(
        Events $event,
        Collection $eventUsers,
        int $delayPerMsg = 2,
        int $batchDelay = 300,
        string $logPrefix = 'EventReminderService',
        ?callable $outputCallback = null
    ): array {
        $emit = function (string $level, string $message) use ($logPrefix, $outputCallback): void {
            Log::{$level}("{$logPrefix}: {$message}");
            if ($outputCallback) {
                $outputCallback($level, $message);
            }
        };

        // تحديد اسم اليوم بالعربي
        $dayNameEnglish = Carbon::parse($event->date)->format('l');
        $dayNameArabic  = self::ARABIC_DAYS[$dayNameEnglish] ?? $dayNameEnglish;
        $dateWithDay    = $dayNameArabic . ' ' . $event->date;

        // تنسيق الوقت إلى نظام 12 ساعة مع AM / PM
        $formattedTime = $event->time;
        if (!empty($event->time)) {
            try {
                $formattedTime = Carbon::parse($event->time)->format('g:i A');
            } catch (\Throwable $e) {
                $formattedTime = $event->time;
            }
        }

        // إعداد بيانات الإرسال (مطابقة تماماً لـ remember_users_to_event)
        $whatsSetting   = get_whats_setting($event);
        $token          = $whatsSetting['token'];
        $phone_numer_id = self::getPhoneId($event->phone_setting_id, $whatsSetting['phone_numer_id'] ?? null);
        $url_image      = WhatsAppMediaOptimizerService::getOptimizedEventHeaderImage($event);
        $url_button     = '?q=' . ($event->lat ?? '0') . ',' . ($event->long ?? '0');
        $template_name  = 'car_msg3_';
        $language       = 'ar';

        Log::info("{$logPrefix}: إعدادات الإرسال للحدث ID: {$event->id}", [
            'phone_setting_id' => $event->phone_setting_id,
            'phone_numer_id'   => $phone_numer_id,
            'country_code'     => $event->country_code,
            'url_image'        => $url_image,
            'url_button'       => $url_button,
            'date_param'       => $dateWithDay,
            'time_param'       => $formattedTime,
        ]);

        $processedCount = 0;
        $acceptedCount  = 0;
        $failedCount    = 0;
        $totalUsersInEvent = $eventUsers->count();

        foreach ($eventUsers as $eventUser) {
            $mobile = $eventUser->mobile;
            if (empty($mobile)) {
                Log::warning("{$logPrefix}: رقم موبايل فارغ - EventUser ID: {$eventUser->id}");
                continue;
            }

            $to       = str_replace('+', '', trim($mobile));
            $sendType = ($eventUser->send_type === 'meta') ? 'old_send' : 'new_send';

            if ($sendType === 'old_send') {
                $sent = self::sendViaMeta(
                    $event,
                    $eventUser,
                    $to,
                    $template_name,
                    $language,
                    $url_image,
                    $formattedTime,
                    $dateWithDay,
                    $phone_numer_id,
                    $token,
                    $url_button,
                    $emit
                );
            } else {
                $sent = self::sendViaUltraMsg(
                    $event,
                    $eventUser,
                    $to,
                    $url_image,
                    $dateWithDay,
                    $formattedTime,
                    $emit
                );
            }

            if ($sent) {
                $acceptedCount++;
            } else {
                $failedCount++;
            }

            $processedCount++;

            // بعد كل 20 رسالة انتظر batchDelay (إلا لو كانت آخر رسالة في الحدث)
            if ($processedCount % 20 === 0 && $processedCount < $totalUsersInEvent) {
                $emit('info', "⏳ تم إرسال {$processedCount} من {$totalUsersInEvent} رسالة - انتظار {$batchDelay} ثانية قبل الدفعة التالية...");
                sleep($batchDelay);
            } elseif ($delayPerMsg > 0 && $processedCount < $totalUsersInEvent) {
                sleep($delayPerMsg);
            }
        }

        $emit('info', "اكتمل إرسال الحدث ID: {$event->id} - إجمالي المعالج: {$processedCount} | مقبول/مرسل: {$acceptedCount} | فشل: {$failedCount}");

        return [
            'processed' => $processedCount,
            'accepted'  => $acceptedCount,
            'failed'    => $failedCount,
        ];
    }

    /**
     * Send reminder via Meta Cloud API.
     * Saves message_id without setting remember = 1 (confirmed asynchronously by Webhook).
     */
    private static function sendViaMeta(
        Events $event,
        EventUsers $eventUser,
        string $to,
        string $templateName,
        string $language,
        string $urlImage,
        string $formattedTime,
        string $dateWithDay,
        $phoneNumberId,
        ?string $token,
        string $urlButton,
        callable $emit
    ): bool {
        try {
            $response = SendCarMsgTemplate(
                $to,
                $templateName,
                $language,
                $urlImage,
                $event->name,    // {{1}} اسم المناسبة
                $formattedTime,  // {{2}} الوقت بنظام 12 ساعة AM / PM
                $dateWithDay,    // {{3}} التاريخ + اليوم
                $phoneNumberId,
                $token,
                $urlButton
            );

            $statusCode = $response ? $response->getStatusCode() : null;
            $bodyRaw    = $response ? (string) $response->getBody() : '';
            $bodyData   = json_decode($bodyRaw, true);

            if ($statusCode == 200) {
                $sentMessageId = $bodyData['messages'][0]['id'] ?? null;

                // حفظ message_id فقط بدون وضع remember = 1
                // تأكيد وصول التذكير (remember = 1) سيتم عبر الـ Webhook عند وصول delivered أو read
                if ($sentMessageId) {
                    $eventUser->update(['message_id' => $sentMessageId]);
                }

                WattsChatModel::create([
                    'phone'          => $to,
                    'name'           => 'Admin',
                    'message'        => $templateName,
                    'is_sent_by_me'  => true,
                    'message_id'     => $sentMessageId ?? 0,
                    'from'           => 'Admin',
                    'template_name'  => $templateName,
                    'event_user_id'  => $eventUser->id,
                    'event_id'       => $event->id,
                    'phone_numer_id' => $phoneNumberId,
                ]);

                $emit('info', "📤 [{$eventUser->id}] تم قبول الطلب من ميتا للرقم {$to} (Message ID: {$sentMessageId}) - بانتظار تأكيد التسليم عبر الـ Webhook");
                return true;
            }

            $errorMsg     = $bodyData['error']['message'] ?? $bodyRaw;
            $errorDetails = $bodyData['error']['error_data']['details'] ?? '';
            $eventUser->update(['status' => 'failed-v2', 'remember' => 0]);
            $emit('error', "❌ [{$eventUser->id}] فشل الإرسال من ميتا للرقم {$to} (HTTP {$statusCode}): {$errorMsg} | {$errorDetails}");
            return false;
        } catch (\Exception $e) {
            $eventUser->update(['status' => 'failed-v2', 'remember' => 0]);
            $emit('error', "❌ [{$eventUser->id}] استثناء أثناء الإرسال (old_send) للرقم {$to}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send reminder via UltraMsg API.
     */
    private static function sendViaUltraMsg(
        Events $event,
        EventUsers $eventUser,
        string $to,
        string $urlImage,
        string $dateWithDay,
        string $formattedTime,
        callable $emit
    ): bool {
        $ultramsgToken = config('services.ultramsg.token');
        $instanceId    = config('services.ultramsg.instance_id');

        if (empty($ultramsgToken) || empty($instanceId)) {
            $emit('error', "❌ [{$eventUser->id}] بيانات UltraMsg غير مضبوطة في .env (ULTRAMSG_TOKEN / ULTRAMSG_INSTANCE_ID)");
            return false;
        }

        $client  = new \UltraMsg\WhatsAppApi($ultramsgToken, $instanceId);
        $caption = "ضيفتنـا الغاليـة , ننتظـرك يوم " . $dateWithDay
            . " في تمــام الساعة " . $formattedTime
            . "  تشرفينــا لحضور " . $event->name . ' 🌺🌺 ';

        try {
            $api = $client->sendImageMessage($to, $urlImage, $caption, 0, 'SDK', true);
            $client->sendLocationMessage($to, $event->address, $event->lat, $event->long, 0, 'SDK');

            if (!empty($api) && isset($api['sent']) && $api['sent'] == 'true'
                && isset($api['message']) && $api['message'] == 'ok') {
                $eventUser->update(['remember' => 1]);
                $emit('info', "✅ [{$eventUser->id}] تم الإرسال (UltraMsg) إلى {$to}");
                return true;
            }

            $errResp = json_encode($api, JSON_UNESCAPED_UNICODE);
            $emit('error', "❌ [{$eventUser->id}] فشل الإرسال (UltraMsg) إلى {$to}: {$errResp}");
            return false;
        } catch (\Exception $e) {
            $emit('error', "❌ [{$eventUser->id}] خطأ (UltraMsg) للرقم {$to}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * جلب phone_numer_id من NewSetting أو من إعدادات الدولة في Setting
     */
    public static function getPhoneId($id, $fallbackPhoneId = null)
    {
        if (!empty($id)) {
            $data = NewSetting::where('id', $id)->first();
            if (!empty($data) && !empty($data->phone_numer_id)) {
                return $data->phone_numer_id;
            }
        }

        if (!empty($fallbackPhoneId)) {
            return $fallbackPhoneId;
        }

        return Setting::first()?->phone_numer_id ?? null;
    }
}
