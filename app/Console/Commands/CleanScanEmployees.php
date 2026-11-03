<?php

namespace App\Console\Commands;

use App\Models\CustomEvent;
use App\Models\Events;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanScanEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan-employees:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'حذف موظفي المسح الذين ليس لديهم مناسبات ميعادها لم يمر منذ أكثر من يومين';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cutoffDate = Carbon::today()->subDays(2)->toDateString();

        Log::info("CleanScanEmployees: بدء فحص موظفي المسح لتاريخ فاصل: {$cutoffDate}");
        $this->info("CleanScanEmployees: بدء فحص موظفي المسح لتاريخ فاصل: {$cutoffDate}");

        // 1. جلب ID موظفي المسح المرتبطين بمناسبات لم يمر عليها أكثر من يومين
        $activeEventsIds = Events::withoutGlobalScopes()
            ->whereNotNull('scan_assistant_id')
            ->where('date', '>=', $cutoffDate)
            ->pluck('scan_assistant_id')
            ->toArray();

        $activeCustomIds = [];
        if (class_exists(CustomEvent::class)) {
            try {
                $activeCustomIds = CustomEvent::withoutGlobalScopes()
                    ->whereNotNull('scan_assistant_id')
                    ->where('date', '>=', $cutoffDate)
                    ->pluck('scan_assistant_id')
                    ->toArray();
            } catch (\Throwable $e) {
                // في حالة عدم وجود الجدول
            }
        }

        $activeIds = array_unique(array_filter(array_merge($activeEventsIds, $activeCustomIds)));

        // 2. تحديد موظفي المسح الذين لا يملكون أي مناسبة ميعادها صالح
        $query = User::withoutGlobalScopes()
            ->where('user_type', 'scan_employee')
            ->whereNotIn('id', $activeIds);

        $count = $query->count();

        if ($count > 0) {
            $deletedIds = $query->pluck('id')->toArray();

            // فك الارتباط من أي مناسبات سابقة قبل الحذف
            Events::withoutGlobalScopes()
                ->whereIn('scan_assistant_id', $deletedIds)
                ->update(['scan_assistant_id' => null]);

            if (class_exists(CustomEvent::class)) {
                try {
                    CustomEvent::withoutGlobalScopes()
                        ->whereIn('scan_assistant_id', $deletedIds)
                        ->update(['scan_assistant_id' => null]);
                } catch (\Throwable $e) {
                }
            }

            // حذف الموظفين
            User::withoutGlobalScopes()
                ->whereIn('id', $deletedIds)
                ->delete();

            $msg = "تم حذف {$count} من موظفي المسح (scan_employee) بنجاح.";
            $this->info($msg);
            Log::info("CleanScanEmployees: {$msg}");
        } else {
            $msg = "لا يوجد موظفي مسح تنطبق عليهم شروط الحذف.";
            $this->info($msg);
            Log::info("CleanScanEmployees: {$msg}");
        }

        return 0;
    }
}
