<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Models\EventUsers;
use App\Models\WattsChat as WattsChatModel;
use App\Services\EventReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
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
                            {--datetime= : تاريخ ووقت الفحص المرجعي (افتراضياً الآن بتوقيت القاهرة)}
                            {--delay=2 : ثواني الانتظار بين كل رسالة}
                            {--batch-delay=300 : ثواني الانتظار بعد كل 20 رسالة}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال رسائل التذكير للمستخدمين المجدول لهم remeber_at عند حلول التاريخ والوقت ولم يتم تذكيرهم بعد (remember = 0)';

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

        $now = $this->option('datetime')
            ? Carbon::parse($this->option('datetime'), 'Africa/Cairo')
            : Carbon::now('Africa/Cairo');

        $delayPerMsg = (int) ($this->option('delay') ?? 2);
        $batchDelay  = (int) ($this->option('batch-delay') ?? 300);

        $nowStr = $now->toDateTimeString();
        Log::info("RemindScheduledEventUsers: بدء فحص التذكيرات المجدولة حتى: {$nowStr}");
        $this->info("RemindScheduledEventUsers: بدء فحص التذكيرات المجدولة حتى: {$nowStr}");

        $scheduledUsers = EventUsers::whereNotNull('remeber_at')
            ->where('remeber_at', '<=', $nowStr)
            ->where(function ($q) {
                $q->whereNull('remember')->orWhere('remember', 0);
            })
            ->get()
            ->filter(function (EventUsers $user) {
                // منع تكرار المحاولة كل دقيقة لنفس الموعد المجدول إذا فشلت الرسالة أو بانتظار الـ Webhook
                $cacheKey = 'remind_scheduled_attempted_' . $user->id . '_' . $user->remeber_at;
                if (Cache::has($cacheKey)) {
                    return false;
                }

                $alreadyAttemptedInDb = WattsChatModel::where('event_user_id', $user->id)
                    ->where('template_name', 'car_msg3_')
                    ->where('created_at', '>=', $user->remeber_at)
                    ->exists();

                return !$alreadyAttemptedInDb;
            });

        if ($scheduledUsers->isEmpty()) {
            Log::info("RemindScheduledEventUsers: لا يوجد مستخدمين مجدول لهم تذكير مستحق حتى {$nowStr} (remember = 0)");
            $this->info("لا يوجد مستخدمين مجدول لهم تذكير مستحق حتى {$nowStr}");
            return 0;
        }

        // تعليم المستخدمين كمُعالجين لهذا الموعد قبل الإرسال لمنع التكرار عند تشغيل الكرون في الدقيقة التالية
        foreach ($scheduledUsers as $user) {
            Cache::put(
                'remind_scheduled_attempted_' . $user->id . '_' . $user->remeber_at,
                true,
                now()->addDays(2)
            );
        }

        $groupedByEvent = $scheduledUsers->groupBy('event_id');
        $events = Events::withoutGlobalScopes()
            ->whereIn('id', $groupedByEvent->keys())
            ->get()
            ->keyBy('id');

        Log::info("RemindScheduledEventUsers: تم العثور على {$scheduledUsers->count()} مستخدم عبر {$groupedByEvent->count()} حدث/أحداث حتى {$nowStr}");
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
