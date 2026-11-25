<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Models\EventUsers;
use App\Services\EventReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class RemindScheduledEventUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:remind-scheduled
                            {--date= : تاريخ التذكير المستهدف (افتراضياً اليوم بتوقيت القاهرة)}
                            {--delay=2 : ثواني الانتظار بين كل رسالة}
                            {--batch-delay=300 : ثواني الانتظار بعد كل 20 رسالة}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال رسائل التذكير للمستخدمين المجدول لهم remeber_at اليوم ولم يتم تذكيرهم بعد (remember = 0)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // إجبار لارافل في الـ CLI / Cron Job على استخدام الدومين الحقيقي بدلاً من http://localhost
        $appUrl = config('app.url');
        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            URL::forceRootUrl('https://mazoom.online');
            URL::forceScheme('https');
        }

        set_time_limit(0);

        $targetDate  = $this->option('date') ?: Carbon::now('Africa/Cairo')->toDateString();
        $delayPerMsg = (int) ($this->option('delay') ?? 2);
        $batchDelay  = (int) ($this->option('batch-delay') ?? 300);

        Log::info("RemindScheduledEventUsers: بدء فحص التذكيرات المجدولة لتاريخ: {$targetDate}");
        $this->info("RemindScheduledEventUsers: بدء فحص التذكيرات المجدولة لتاريخ: {$targetDate}");

        $scheduledUsers = EventUsers::whereDate('remeber_at', $targetDate)
            ->where(function ($q) {
                $q->whereNull('remember')->orWhere('remember', 0);
            })
            ->get();

        if ($scheduledUsers->isEmpty()) {
            Log::info("RemindScheduledEventUsers: لا يوجد مستخدمين مجدول لهم تذكير بتاريخ {$targetDate} (remember = 0)");
            $this->info("لا يوجد مستخدمين مجدول لهم تذكير بتاريخ {$targetDate}");
            return 0;
        }

        $groupedByEvent = $scheduledUsers->groupBy('event_id');
        $events = Events::withoutGlobalScopes()
            ->whereIn('id', $groupedByEvent->keys())
            ->get()
            ->keyBy('id');

        Log::info("RemindScheduledEventUsers: تم العثور على {$scheduledUsers->count()} مستخدم عبر {$groupedByEvent->count()} حدث/أحداث لتاريخ {$targetDate}");
        $this->info("تم العثور على {$scheduledUsers->count()} مستخدم عبر {$groupedByEvent->count()} حدث/أحداث");

        foreach ($groupedByEvent as $eventId => $eventUsers) {
            $event = $events->get($eventId);
            if (!$event) {
                Log::warning("RemindScheduledEventUsers: الحدث ID {$eventId} غير موجود أو محذوف - تم تخطي {$eventUsers->count()} مستخدم");
                $this->warn("⚠️ الحدث ID {$eventId} غير موجود - تم التخطي");
                continue;
            }

            Log::info("RemindScheduledEventUsers: معالجة الحدث: {$event->name} (ID: {$event->id}) | عدد المستخدمين: {$eventUsers->count()}");
            $this->info("معالجة الحدث: {$event->name} (ID: {$event->id}) | عدد المستخدمين: {$eventUsers->count()}");

            EventReminderService::sendRemindersForEvent(
                $event,
                $eventUsers,
                $delayPerMsg,
                $batchDelay,
                'RemindScheduledEventUsers',
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

        Log::info("RemindScheduledEventUsers: ✅ اكتمل إرسال جميع التذكيرات المجدولة.");
        $this->info("✅ اكتمل إرسال جميع التذكيرات المجدولة.");

        return 0;
    }
}
