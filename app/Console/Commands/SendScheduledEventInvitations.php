<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Models\EventUsers;
use App\Services\EventInvitationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendScheduledEventInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-scheduled-invitations
                            {--event_id= : معرف حدث محدد للإرسال له فقط}
                            {--delay=1 : عدد ثواني الانتظار بين كل رسالة ورسالة}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'فحص وإرسال الدعوات المجدولة للأحداث التي حان موعد إرسالها (send_date <= now)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // إجبار الرابط الكامل على النطاق الحقيقي حتى تتمكن ميتا من جلب ملفات الوسائط
        $appUrl = config('app.url');
        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            URL::forceRootUrl('https://mazoom.online');
            URL::forceScheme('https');
        }

        set_time_limit(0);

        $delay = (int) ($this->option('delay') ?? 1);
        $specificEventId = $this->option('event_id');

        $now = Carbon::now();
        $this->info("🕒 بدء تشغيل أمر إرسال الدعوات المجدولة في: {$now->toDateTimeString()}");
        Log::info("SendScheduledEventInvitations: Started check at {$now->toDateTimeString()}");

        if ($specificEventId) {
            $event = Events::withoutGlobalScopes()->find($specificEventId);
            if (!$event) {
                $this->error("❌ لم يتم العثور على الحدث برقم: {$specificEventId}");
                return 1;
            }

            $this->info("معالجة الحدث المحدد: {$event->title} (ID: {$event->id})");
            $result = EventInvitationService::sendPendingInvitationsForEvent($event, $delay);

            $this->info("✅ النتيجة: إجمالي {$result['total']} | تم إرسال: {$result['sent']} | فشل: {$result['failed']}");
            return 0;
        }

        // جلب الأحداث التي حان موعد إرسالها ولديها دعوات معلقة
        $events = Events::withoutGlobalScopes()
            ->whereNotNull('send_date')
            ->where('send_date', '<=', $now)
            ->whereHas('event_users', function ($q) {
                $q->where('status', 'hold')
                  ->where('is_new_sent', 0)
                  ->whereNull('is_sent');
            })
            ->get();

        if ($events->isEmpty()) {
            $this->info("لا توجد أحداث مجدولة تحتاج للإرسال حالياً.");
            return 0;
        }

        $this->info("تم العثور على {$events->count()} حدث/أحداث جاهزة للإرسال.");

        $totalSent = 0;
        $totalFailed = 0;

        foreach ($events as $event) {
            $pendingCount = EventUsers::where('event_id', $event->id)
                ->where('status', 'hold')
                ->where('is_new_sent', 0)
                ->whereNull('is_sent')
                ->count();

            $this->info("➡️ معالجة الحدث: {$event->title} (ID: {$event->id}) | عدد الدعوات المعلقة: {$pendingCount} | نوع الإرسال: " . ($event->send_type ?? 'image'));

            $res = EventInvitationService::sendPendingInvitationsForEvent($event, $delay);

            $totalSent += $res['sent'];
            $totalFailed += $res['failed'];

            $this->info("✔️ الحدث ID {$event->id}: تم بنجاح: {$res['sent']} | فشل: {$res['failed']}");
        }

        $this->info("🎉 اكتملت المعالجة: إجمالي تم إرساله: {$totalSent} | إجمالي الفاشل: {$totalFailed}");
        Log::info("SendScheduledEventInvitations: Completed. Total Sent: {$totalSent}, Total Failed: {$totalFailed}");

        return 0;
    }
}
