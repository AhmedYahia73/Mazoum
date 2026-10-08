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
    protected $signature = 'events:remind-tomorrow {--date= : تاريخ الفعاليات (افتراضياً غداً)} {--initial-delay=300 : مدة الانتظار قبل أول دفعة بالثواني (افتراضياً 300 ثانية = 5 دقائق)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تذكير المستخدمين بالأحداث القادمة غداً - يبدأ بعد 5 دقائق ويرسل لكل 20 مستخدم ثم ينتظر 5 دقائق';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $targetDate = $this->option('date') ?: Carbon::tomorrow()->toDateString();

        Log::info("RemindTomorrowEvents: بدء البحث عن أحداث التاريخ: {$targetDate}");

        // جلب الأحداث التي تاريخها محدد - بدون Global Scope
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

        // بدء الإرسال بعد 5 دقائق (300 ثانية) افتراضياً
        $totalDelay = (int) ($this->option('initial-delay') ?? 300);

        foreach ($events as $event) {

            Log::info("RemindTomorrowEvents: معالجة الحدث: {$event->name} (ID: {$event->id})");

            // جلب جميع المستخدمين في هذا الحدث
            $eventUsers = EventUsers::where('event_id', $event->id)->get();

            if ($eventUsers->isEmpty()) {
                Log::info("RemindTomorrowEvents: لا يوجد مستخدمين في الحدث ID: {$event->id}");
                continue;
            }

            Log::info("RemindTomorrowEvents: عدد المستخدمين: {$eventUsers->count()} في الحدث ID: {$event->id}");

            // تقسيم المستخدمين إلى مجموعات من 20
            $chunks = $eventUsers->chunk(20);

            foreach ($chunks as $chunkIndex => $chunk) {

                // تحديد اسم اليوم بالعربي
                $dayNameEnglish = Carbon::parse($event->date)->format('l');
                $dayNameArabic = $arabicDays[$dayNameEnglish] ?? $dayNameEnglish;
                $dateWithDay = $event->date . ' ' . $dayNameArabic;

                // تحديد نوع الإرسال بناءً على send_type لكل مستخدم
                foreach ($chunk as $eventUser) {

                    $sendingType = $this->getSendingType($eventUser);
                    $message = $event->name;
                    $date = $dateWithDay;
                    $time = $event->time;
                    $phoneSettingId = $event->phone_setting_id;

                    // dispatch job مع delay
                    \App\Jobs\RemindEventUserJob::dispatch(
                        $eventUser->id,
                        $event->id,
                        $sendingType,
                        $message,
                        $date,
                        $time,
                        $phoneSettingId
                    )->delay(now()->addSeconds($totalDelay));
                }

                Log::info("RemindTomorrowEvents: تم جدولة المجموعة " . ($chunkIndex + 1) . " ({$chunk->count()} مستخدم) بتأخير {$totalDelay} ثانية");

                // انتظار 5 دقائق بعد كل مجموعة من 20
                $totalDelay += 300; // 5 دقائق = 300 ثانية
            }

            // انتظار 5 دقائق إضافية بين الأحداث
            // (بالفعل تمت إضافة 5 دقائق بعد آخر مجموعة، فهذا يضمن الفاصل بين الأحداث)
            Log::info("RemindTomorrowEvents: اكتمل جدولة الحدث ID: {$event->id}");
        }

        $totalMinutes = $totalDelay / 60;
        Log::info("RemindTomorrowEvents: تم جدولة جميع الرسائل. إجمالي وقت التأخير: {$totalMinutes} دقيقة");
        $this->info("تم جدولة جميع الرسائل بنجاح. إجمالي وقت التأخير: {$totalMinutes} دقيقة");

        return 0;
    }

    /**
     * تحديد نوع الإرسال بناءً على send_type في EventUsers
     * meta => old_send
     * link => new_send
     */
    private function getSendingType(EventUsers $eventUser): string
    {
        if ($eventUser->send_type === 'meta') {
            return 'old_send';
        }

        // link أو أي قيمة أخرى
        return 'new_send';
    }
}
