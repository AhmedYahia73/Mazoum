<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Services\EventReminderService;
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
                            {--target=all : الفئة المستهدفة: all (الطرفين - افتراضي), confirmed (المؤكدين), unconfirmed (غير المؤكدين)}
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

        set_time_limit(0);

        $targetDate     = $this->option('date') ?: Carbon::tomorrow()->toDateString();
        $targetAudience = $this->option('target') ?: 'all';
        $initialDelay   = (int) ($this->option('initial-delay') ?? 0);
        $delayPerMsg    = (int) ($this->option('delay') ?? 2);        // ثانيتان بين كل رسالة
        $batchDelay     = (int) ($this->option('batch-delay') ?? 300); // 5 دقائق بعد كل 20 رسالة

        Log::info("RemindTomorrowEvents: بدء البحث عن أحداث التاريخ: {$targetDate} | الفئة المستهدفة: {$targetAudience}");
        $this->info("RemindTomorrowEvents: بدء البحث عن أحداث التاريخ: {$targetDate} | الفئة المستهدفة: {$targetAudience}");

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

        foreach ($events as $event) {
            Log::info("RemindTomorrowEvents: معالجة الحدث: {$event->name} (ID: {$event->id})");
            $this->info("معالجة الحدث: {$event->name} (ID: {$event->id})");

            // استعلام جلب المدعوين للتذكير بناءً على الفئة المستهدفة (--target: all / confirmed / unconfirmed)
            $eventUsers = EventReminderService::eligibleForReminderQuery($event->id, $targetAudience)->get();

            if ($eventUsers->isEmpty()) {
                Log::info("RemindTomorrowEvents: لا يوجد مستخدمين بحاجة للتذكير (الفئة: {$targetAudience}) في الحدث ID: {$event->id}");
                $this->warn("لا يوجد مستخدمين بحاجة للتذكير (الفئة: {$targetAudience}) في الحدث ID: {$event->id}");
                continue;
            }

            Log::info("RemindTomorrowEvents: عدد المستخدمين ({$targetAudience}): {$eventUsers->count()} في الحدث ID: {$event->id}");
            $this->info("عدد المستخدمين ({$targetAudience}): {$eventUsers->count()} في الحدث ID: {$event->id}");

            EventReminderService::sendRemindersForEvent(
                $event,
                $eventUsers,
                $delayPerMsg,
                $batchDelay,
                'RemindTomorrowEvents',
                function (string $level, string $message): void {
                    if ($level === 'error') {
                        $this->error($message);
                    } elseif ($level === 'warning') {
                        $this->warn($message);
                    } else {
                        $this->info($message);
                    }
                }
            );
        }

        Log::info("RemindTomorrowEvents: ✅ اكتمل إرسال جميع التذكيرات.");
        $this->info("✅ اكتمل إرسال جميع التذكيرات.");

        return 0;
    }
}
