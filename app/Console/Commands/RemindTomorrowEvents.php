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

class RemindTomorrowEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:remind-tomorrow
                            {--date= : تاريخ الفعاليات (افتراضياً غداً)}
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
        $targetDate  = $this->option('date') ?: Carbon::tomorrow()->toDateString();
        $delayPerMsg = (int) ($this->option('delay') ?? 2);        // ثانيتان بين كل رسالة
        $batchDelay  = (int) ($this->option('batch-delay') ?? 300); // 5 دقائق بعد كل 20

        Log::info("RemindTomorrowEvents: بدء البحث عن أحداث التاريخ: {$targetDate}");

        // جلب الأحداث بدون Global Scope (الكرون يعمل بدون مستخدم مسجّل)
        $events = Events::withoutGlobalScopes()->where('date', $targetDate)->get();

        if ($events->isEmpty()) {
            Log::info("RemindTomorrowEvents: لا توجد أحداث بتاريخ {$targetDate}");
            $this->info("لا توجد أحداث بتاريخ {$targetDate}");
            return 0;
        }

        Log::info("RemindTomorrowEvents: تم العثور على {$events->count()} حدث/أحداث بتاريخ {$targetDate}");

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

            // جلب المستخدمين الذين لم يُرسل لهم تذكير بعد فقط
            $eventUsers = EventUsers::where('event_id', $event->id)
                ->where('remember', 0)
                ->get();

            if ($eventUsers->isEmpty()) {
                Log::info("RemindTomorrowEvents: لا يوجد مستخدمين جدد في الحدث ID: {$event->id}");
                continue;
            }

            Log::info("RemindTomorrowEvents: عدد المستخدمين: {$eventUsers->count()} في الحدث ID: {$event->id}");

            // تحديد اسم اليوم بالعربي
            $dayNameEnglish = Carbon::parse($event->date)->format('l');
            $dayNameArabic  = $arabicDays[$dayNameEnglish] ?? $dayNameEnglish;
            $dateWithDay    = $event->date . ' ' . $dayNameArabic;

            // إعداد بيانات الإرسال (مشتركة لجميع مستخدمي هذا الحدث)
            $token          = get_whats_setting($event)['token'];
            $phone_numer_id = $this->getPhoneId($event->phone_setting_id);
            $url_image      = $event->file; // Image_Path accessor يُرجع URL كامل
            $url_button     = '?q=' . ($event->lat ?? '0') . ',' . ($event->long ?? '0');
            $template_name  = 'car_msg3_';
            $language       = 'ar';

            Log::info("RemindTomorrowEvents: بيانات الإرسال", [
                'event_id'       => $event->id,
                'phone_numer_id' => $phone_numer_id,
                'url_image'      => $url_image,
                'url_button'     => $url_button,
                'token_preview'  => substr($token ?? '', 0, 20) . '...',
            ]);

            $sentCount = 0;

            foreach ($eventUsers as $eventUser) {

                $mobile = $eventUser->mobile;
                if (empty($mobile)) {
                    Log::warning("RemindTomorrowEvents: رقم موبايل فارغ - EventUser ID: {$eventUser->id}");
                    continue;
                }

                $to       = str_replace('+', '', $mobile);
                $sendType = ($eventUser->send_type === 'meta') ? 'old_send' : 'new_send';

                Log::info("RemindTomorrowEvents: إرسال للمستخدم ID: {$eventUser->id} | to: {$to} | type: {$sendType}");

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

                        Log::info("RemindTomorrowEvents: استجابة ميتا", [
                            'event_user_id' => $eventUser->id,
                            'to'            => $to,
                            'status_code'   => $statusCode,
                            'response'      => $bodyData,
                        ]);

                        if ($statusCode == 200) {
                            $eventUser->update(['remember' => 1]);

                            WattsChatModel::create([
                                'phone'          => $to,
                                'name'           => 'Admin',
                                'message'        => $template_name,
                                'is_sent_by_me'  => true,
                                'message_id'     => 0,
                                'from'           => 'Admin',
                                'template_name'  => $template_name,
                                'event_user_id'  => $eventUser->id,
                                'event_id'       => $event->id,
                                'phone_numer_id' => $phone_numer_id,
                            ]);

                            Log::info("RemindTomorrowEvents: تم وصول الرسالة بنجاح - EventUser ID: {$eventUser->id}");
                        } else {
                            $eventUser->update(['status' => 'failed-v2']);
                            Log::error("RemindTomorrowEvents: فشل إرسال الرسالة - EventUser ID: {$eventUser->id} - Response: {$bodyRaw}");
                        }

                    } catch (\Exception $e) {
                        Log::error("RemindTomorrowEvents: خطأ في الإرسال - EventUser ID: {$eventUser->id} - Error: {$e->getMessage()}");
                        $eventUser->update(['status' => 'failed-v2']);
                    }

                } else {

                    // إرسال عبر UltraMsg
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
                            Log::info("RemindTomorrowEvents: تم وصول الرسالة بنجاح (new_send) - EventUser ID: {$eventUser->id}");
                        } else {
                            Log::error("RemindTomorrowEvents: فشل الإرسال (new_send) - EventUser ID: {$eventUser->id} - Response: " . json_encode($api));
                        }

                    } catch (\Exception $e) {
                        Log::error("RemindTomorrowEvents: خطأ في الإرسال (new_send) - EventUser ID: {$eventUser->id} - Error: {$e->getMessage()}");
                    }
                }

                $sentCount++;

                // انتظار بين كل رسالة لتفادي الـ rate limit
                if ($delayPerMsg > 0) {
                    sleep($delayPerMsg);
                }

                // بعد كل 20 رسالة انتظر 5 دقائق
                if ($sentCount % 20 === 0) {
                    Log::info("RemindTomorrowEvents: تم إرسال {$sentCount} رسالة - انتظار {$batchDelay} ثانية...");
                    sleep($batchDelay);
                }
            }

            Log::info("RemindTomorrowEvents: اكتمل إرسال الحدث ID: {$event->id} - إجمالي المُرسل: {$sentCount}");
        }

        Log::info("RemindTomorrowEvents: اكتمل إرسال جميع التذكيرات.");
        $this->info("اكتمل إرسال جميع التذكيرات.");

        return 0;
    }

    /**
     * جلب phone_numer_id من NewSetting
     */
    private function getPhoneId($id)
    {
        $data = NewSetting::where('id', $id)->first();

        if (empty($data)) {
            return Setting::first()?->phone_numer_id ?? null;
        }

        return $data->phone_numer_id;
    }
}
