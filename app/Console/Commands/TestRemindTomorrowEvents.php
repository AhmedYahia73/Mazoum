<?php

namespace App\Console\Commands;

use App\Models\Events;
use App\Models\EventUsers;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestRemindTomorrowEvents extends Command
{
    protected $signature = 'events:test-remind-tomorrow {--dry-run : عرض البيانات فقط بدون إرسال} {--date= : تاريخ الحدث (افتراضياً غداً)}';

    protected $description = 'تيست: عرض الأحداث والمستخدمين اللي هيتبعتلهم تذكير بكرة';

    public function handle()
    {
        $targetDate = $this->option('date') ?: Carbon::tomorrow()->toDateString();

        $this->info("========================================");
        $this->info("📅 التاريخ المستهدف: {$targetDate}");
        $this->info("========================================");

        // جلب أحداث التاريخ المحدد
        $events = Events::withoutGlobalScopes()->where('date', $targetDate)->get();

        if ($events->isEmpty()) {
            $this->warn("❌ لا توجد أحداث غداً ({$tomorrow})");
            $this->info("");
            $this->info("💡 للتيست: ممكن تعدل تاريخ حدث موجود لبكرة، أو استخدم:");
            $this->info("   php artisan tinker");
            $this->info("   >>> App\\Models\\Events::withoutGlobalScopes()->latest()->first()->update(['date' => '" . $tomorrow . "']);");
            return 0;
        }

        $this->info("✅ تم العثور على {$events->count()} حدث/أحداث غداً");
        $this->info("");

        $arabicDays = [
            'Sunday'    => 'الأحد',
            'Monday'    => 'الاثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
            'Saturday'  => 'السبت',
        ];

        $totalUsers = 0;
        $totalBatches = 0;

        foreach ($events as $event) {

            $dayNameEnglish = Carbon::parse($event->date)->format('l');
            $dayNameArabic = $arabicDays[$dayNameEnglish] ?? $dayNameEnglish;

            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("📌 حدث: {$event->name} (ID: {$event->id})");
            $this->info("   📅 التاريخ: {$event->date} {$dayNameArabic}");
            $this->info("   ⏰ الوقت: {$event->time}");
            $this->info("   📱 phone_setting_id: {$event->phone_setting_id}");
            $this->info("");

            $eventUsers = EventUsers::where('event_id', $event->id)->get();

            if ($eventUsers->isEmpty()) {
                $this->warn("   ⚠️ لا يوجد مستخدمين في هذا الحدث");
                continue;
            }

            $this->info("   👥 عدد المستخدمين: {$eventUsers->count()}");

            $chunks = $eventUsers->chunk(20);
            $batchNum = 0;
            $delayMinutes = 5;

            foreach ($chunks as $chunkIndex => $chunk) {
                $batchNum++;
                $totalBatches++;
                $this->info("");
                $this->info("   📦 المجموعة {$batchNum} ({$chunk->count()} مستخدم) - يبدأ الإرسال بعد: {$delayMinutes} دقيقة");

                $headers = ['#', 'EventUser ID', 'الاسم', 'الموبايل', 'send_type', 'sending_type2'];
                $rows = [];
                $num = 0;

                foreach ($chunk as $user) {
                    $num++;
                    $totalUsers++;
                    $sendingType = ($user->send_type === 'meta') ? 'old_send' : 'new_send';
                    $rows[] = [
                        $num,
                        $user->id,
                        $user->name ?? '-',
                        $user->mobile ?? '-',
                        $user->send_type ?? 'null',
                        $sendingType,
                    ];
                }

                $this->table($headers, $rows);

                $delayMinutes += 5;
            }
        }

        $this->info("");
        $this->info("========================================");
        $this->info("📊 الملخص:");
        $this->info("   🎯 عدد الأحداث: {$events->count()}");
        $this->info("   👥 إجمالي المستخدمين: {$totalUsers}");
        $this->info("   📦 إجمالي المجموعات (batches): {$totalBatches}");
        $this->info("   ⏱️ إجمالي الوقت المتوقع: " . (($totalBatches) * 5) . " دقيقة");
        $this->info("========================================");

        if ($this->option('dry-run')) {
            $this->warn("🔶 وضع dry-run: لم يتم إرسال أي رسائل");
        } else {
            $this->info("");
            if ($this->confirm('هل تريد تشغيل الإرسال الفعلي الآن؟', false)) {
                $this->info("🚀 جاري تشغيل الإرسال...");
                $this->call('events:remind-tomorrow');
            } else {
                $this->info("تم الإلغاء. لتشغيل الإرسال يدوياً:");
                $this->info("   php artisan events:remind-tomorrow");
            }
        }

        return 0;
    }
}
