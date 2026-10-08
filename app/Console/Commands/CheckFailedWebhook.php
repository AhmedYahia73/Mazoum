<?php

namespace App\Console\Commands;

use App\Models\EventUserLogs;
use App\Models\Logs;
use Illuminate\Console\Command;

class CheckFailedWebhook extends Command
{
    protected $signature = 'check:failed-webhook';

    protected $description = 'عرض تفاصيل آخر خطأ وصل في الـ Webhook من ميتا';

    public function handle()
    {
        $this->info("=========================================");
        $this->info("🔍 البحث في جدول EventUserLogs عن آخر فشل:");
        $this->info("=========================================");

        $eventLog = EventUserLogs::where('status', 'failed')->latest()->first();

        if ($eventLog) {
            $this->warn("Event ID: " . $eventLog->event_id);
            $this->warn("Event User ID: " . $eventLog->event_user_id);
            $this->warn("Message ID: " . $eventLog->message_id);
            $this->error("Error Title: " . ($eventLog->error_title ?? 'null'));
            $this->error("Error Details: " . ($eventLog->error_details ?? 'null'));

            if (!empty($eventLog->log)) {
                $decoded = json_decode($eventLog->log, true);
                $errors = data_get($decoded, 'entry.0.changes.0.value.statuses.0.errors');
                if ($errors) {
                    $this->info("\nتفاصيل الـ errors من ميتا:");
                    $this->line(json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
            return 0;
        }

        $this->info("لم يتم العثور على سجل في EventUserLogs. جاري البحث في جدول Logs...");

        $log = Logs::where('log', 'like', '%failed%')->latest()->first();
        if ($log && !empty($log->log)) {
            $decoded = json_decode($log->log, true);
            $errors = data_get($decoded, 'entry.0.changes.0.value.statuses.0.errors');
            if ($errors) {
                $this->info("\nتفاصيل الـ errors من ميتا من جدول Logs:");
                $this->line(json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                return 0;
            }
            $this->line("محتوى الـ Log:");
            $this->line(substr($log->log, 0, 1000));
            return 0;
        }

        $this->warn("لا توجد سجلات failed في جدول Logs أيضاً.");
        return 0;
    }
}
