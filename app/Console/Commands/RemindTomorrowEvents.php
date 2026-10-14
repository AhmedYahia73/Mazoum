<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Models\EventUsers;
use App\Models\NewSetting;
use App\Models\Setting;
use App\Models\WattsChat as WattsChatModel;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class RemindTomorrowEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:remind-tomorrow
                            {--date= : تاريخ الفعاليات (افتراضياً غداً)}
                            {--initial-delay=0 : ثواني الانتظار قبل بدء أول دفعة}
                            {--delay=2 : ثواني الانتظار بين كل رسالة}
                            {--batch-delay=300 : ثواني الانتظار بعد كل 20 رسالة}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تذكير المستخدمين بالأحداث القادمة غداً - يرسل 20 رسالة ثم ينتظر 5 دقائق';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // إجبار لارافل في الـ CLI / Cron Job على استخدام الدومين الحقيقي بدلاً من http://localhost
        // لأن ميتا ترفض تحميل صور الهيدر لو الرابط http://localhost
        $appUrl = config('app.url');
        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            URL::forceRootUrl('https://mazoom.online');
            URL::forceScheme('https');
        }

        $targetDate   = $this->option('date') ?: Carbon::tomorrow()->toDateString();
        $initialDelay = (int) ($this->option('initial-delay') ?? 0);
        $delayPerMsg  = (int) ($this->option('delay') ?? 2);        // ثانيتان بين كل رسالة
        $batchDelay   = (int) ($this->option('batch-delay') ?? 300); // 5 دقائق بعد كل 20 رسالة

        Log::info("RemindTomorrowEvents: بدء البحث عن أحداث التاريخ: {$targetDate}");
        $this->info("RemindTomorrowEvents: بدء البحث عن أحداث التاريخ: {$targetDate}");

        // جلب الأحداث بدون Global Scope (الكرون يعمل بدون مستخدم مسجّل)
        $events = Events::withoutGlobalScopes()->where('date', $targetDate)->get();

        if ($events->isEmpty()) {
            Log::info("RemindTomorrowEvents: لا توجد أحداث بتاريخ {$targetDate}");
            $this->info("لا توجد أحداث بتاريخ {$targetDate}");
            return 0;
        }

        Log::info("RemindTomorrowEvents: تم العثور على {$events->count()} حدث/أحداث بتاريخ {$targetDate}");
        $this->info("تم العثور على {$events->count()} حدث/أحداث بتاريخ {$targetDate}");

        if ($initialDelay > 0) {
            Log::info("RemindTomorrowEvents: انتظار {$initialDelay} ثانية قبل بدء الإرسال...");
            $this->info("انتظار {$initialDelay} ثانية قبل بدء الإرسال...");
            sleep($initialDelay);
        }

        // أسماء الأيام بالعربي
        $arabicDays = [
            'Sunday'    => 'الأحد',
            'Monday'    => 'الاثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
            'Saturday'  => 'السبت',
        ];

        foreach ($events as $event) {

            Log::info("RemindTomorrowEvents: معالجة الحدث: {$event->name} (ID: {$event->id})");
            $this->info("معالجة الحدث: {$event->name} (ID: {$event->id})");

            // جلب جميع المستخدمين في هذا الحدث (نفس سلوك remember_users_to_event)
            $eventUsers = EventUsers::where('event_id', $event->id)->get();

            if ($eventUsers->isEmpty()) {
                Log::info("RemindTomorrowEvents: لا يوجد مستخدمين في الحدث ID: {$event->id}");
                $this->warn("لا يوجد مستخدمين في الحدث ID: {$event->id}");
                continue;
            }

            Log::info("RemindTomorrowEvents: عدد المستخدمين: {$eventUsers->count()} في الحدث ID: {$event->id}");
            $this->info("عدد المستخدمين: {$eventUsers->count()} في الحدث ID: {$event->id}");

            // تحديد اسم اليوم بالعربي
            $dayNameEnglish = Carbon::parse($event->date)->format('l');
            $dayNameArabic  = $arabicDays[$dayNameEnglish] ?? $dayNameEnglish;
            $dateWithDay    = $dayNameArabic . ' ' . $event->date;

            // إعداد بيانات الإرسال (مطابقة تماماً لـ remember_users_to_event)
            $whatsSetting   = get_whats_setting($event);
            $token          = $whatsSetting['token'];
            $phone_numer_id = $this->getPhoneId($event->phone_setting_id, $whatsSetting['phone_numer_id'] ?? null);
            $url_image      = $event->file;
            $url_image      = preg_replace('#^https?://(localhost|127\.0\.0\.1)(:\d+)?#i', 'https://mazoom.online', $url_image);
            $url_button     = '?q=' . ($event->lat ?? '0') . ',' . ($event->long ?? '0');
            $template_name  = 'car_msg3_';
            $language       = 'ar';

            Log::info("RemindTomorrowEvents: إعدادات الإرسال للحدث ID: {$event->id}", [
                'phone_setting_id' => $event->phone_setting_id,
                'phone_numer_id'   => $phone_numer_id,
                'country_code'     => $event->country_code,
                'url_image'        => $url_image,
                'url_button'       => $url_button,
                'date_param'       => $dateWithDay,
                'time_param'       => $event->time,
            ]);

            $sentCount = 0;
            $totalUsersInEvent = $eventUsers->count();

            foreach ($eventUsers as $eventUser) {

                $mobile = $eventUser->mobile;
                if (empty($mobile)) {
                    Log::warning("RemindTomorrowEvents: رقم موبايل فارغ - EventUser ID: {$eventUser->id}");
                    continue;
                }

                $to       = str_replace('+', '', trim($mobile));
                $sendType = ($eventUser->send_type === 'meta') ? 'old_send' : 'new_send';

                if ($sendType === 'old_send') {

                    // إرسال عبر Meta Cloud API
                    try {
                        $response = SendCarMsgTemplate(
                            $to,
                            $template_name,
                            $language,
                            $url_image,
                            $event->name,  // {{1}} اسم المناسبة
                            $event->time,  // {{2}} الوقت
                            $dateWithDay,  // {{3}} التاريخ + اليوم
                            $phone_numer_id,
                            $token,
                            $url_button
                        );

                        $statusCode = $response ? $response->getStatusCode() : null;
                        $bodyRaw    = $response ? (string) $response->getBody() : '';
                        $bodyData   = json_decode($bodyRaw, true);

                        if ($statusCode == 200) {
                            $sentMessageId = $bodyData['messages'][0]['id'] ?? null;

                            $updateData = ['remember' => 1];
                            if ($sentMessageId) {
                                $updateData['message_id'] = $sentMessageId;
                            }
                            $eventUser->update($updateData);

                            WattsChatModel::create([
                                'phone'          => $to,
                                'name'           => 'Admin',
                                'message'        => $template_name,
                                'is_sent_by_me'  => true,
                                'message_id'     => $sentMessageId ?? 0,
                                'from'           => 'Admin',
                                'template_name'  => $template_name,
                                'event_user_id'  => $eventUser->id,
                                'event_id'       => $event->id,
                                'phone_numer_id' => $phone_numer_id,
                            ]);

                            Log::info("RemindTomorrowEvents: 📤 تم قبول التذكير من ميتا بنجاح (old_send) - EventUser ID: {$eventUser->id} | الرقم: {$to} | Message ID: {$sentMessageId} (سيتم تأكيد الوصول الفعلي عبر الـ Webhook)");
                            $this->info("✅ [{$eventUser->id}] تم الإرسال إلى {$to} (Message ID: {$sentMessageId})");
                        } else {
                            $errorMsg = $bodyData['error']['message'] ?? $bodyRaw;
                            $errorDetails = $bodyData['error']['error_data']['details'] ?? '';
                            $eventUser->update(['status' => 'failed-v2']);
                            Log::error("RemindTomorrowEvents: ❌ فشل إرسال الرسالة من ميتا (old_send) - EventUser ID: {$eventUser->id} | الرقم: {$to} | HTTP {$statusCode} | المشكلة: {$errorMsg} | التفاصيل: {$errorDetails}");
                            $this->error("❌ [{$eventUser->id}] فشل الإرسال إلى {$to}: {$errorMsg}");
                        }

                    } catch (\Exception $e) {
                        Log::error("RemindTomorrowEvents: ❌ استثناء أثناء الإرسال (old_send) - EventUser ID: {$eventUser->id} | الرقم: {$to} | المشكلة: {$e->getMessage()}");
                        $this->error("❌ [{$eventUser->id}] خطأ: {$e->getMessage()}");
                        $eventUser->update(['status' => 'failed-v2']);
                    }

                } else {

                    // إرسال عبر UltraMsg (new_send)
                    $ultramsg_token = "7ye6ifujyug0u46g";
                    $instance_id    = "instance109805";
                    $client         = new \UltraMsg\WhatsAppApi($ultramsg_token, $instance_id);

                    $caption = "ضيفتنـا الغاليـة , ننتظـرك يوم " . $dateWithDay
                        . " في تمــام الساعة " . $event->time
                        . "  تشرفينــا لحضور " . $event->name . ' 🌺🌺 ';

                    try {
                        $api  = $client->sendImageMessage($to, $url_image, $caption, 0, "SDK", true);
                        $api2 = $client->sendLocationMessage($to, $event->address, $event->lat, $event->long, 0, "SDK");

                        if (!empty($api) && isset($api['sent']) && $api['sent'] == 'true'
                            && isset($api['message']) && $api['message'] == 'ok') {
                            $eventUser->update(['remember' => 1]);
                            Log::info("RemindTomorrowEvents: ✅ تم وصول الرسالة بنجاح عبر UltraMsg (new_send) - EventUser ID: {$eventUser->id} | الرقم: {$to}");
                            $this->info("✅ [{$eventUser->id}] تم الإرسال (UltraMsg) إلى {$to}");
                        } else {
                            $errResp = json_encode($api, JSON_UNESCAPED_UNICODE);
                            Log::error("RemindTomorrowEvents: ❌ فشل الإرسال عبر UltraMsg (new_send) - EventUser ID: {$eventUser->id} | الرقم: {$to} | المشكلة: {$errResp}");
                            $this->error("❌ [{$eventUser->id}] فشل الإرسال (UltraMsg) إلى {$to}: {$errResp}");
                        }

                    } catch (\Exception $e) {
                        Log::error("RemindTomorrowEvents: ❌ خطأ في الإرسال عبر UltraMsg (new_send) - EventUser ID: {$eventUser->id} | الرقم: {$to} | المشكلة: {$e->getMessage()}");
                        $this->error("❌ [{$eventUser->id}] خطأ (UltraMsg): {$e->getMessage()}");
                    }
                }

                $sentCount++;

                // بعد كل 20 رسالة انتظر 5 دقائق (إلا لو كانت آخر رسالة في الحدث)
                if ($sentCount % 20 === 0 && $sentCount < $totalUsersInEvent) {
                    Log::info("RemindTomorrowEvents: تم إرسال {$sentCount} من {$totalUsersInEvent} رسالة - انتظار {$batchDelay} ثانية (5 دقائق) قبل الدفعة التالية...");
                    $this->info("⏳ تم إرسال {$sentCount} رسالة - انتظار {$batchDelay} ثانية قبل الدفعة التالية...");
                    sleep($batchDelay);
                } elseif ($delayPerMsg > 0 && $sentCount < $totalUsersInEvent) {
                    sleep($delayPerMsg);
                }
            }

            Log::info("RemindTomorrowEvents: اكتمل إرسال الحدث ID: {$event->id} - إجمالي المُرسل: {$sentCount}");
        }

        Log::info("RemindTomorrowEvents: ✅ اكتمل إرسال جميع التذكيرات.");
        $this->info("✅ اكتمل إرسال جميع التذكيرات.");

        return 0;
    }

    /**
     * جلب phone_numer_id من NewSetting أو من إعدادات الدولة في Setting
     */
    private function getPhoneId($id, $fallbackPhoneId = null)
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
