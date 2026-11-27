<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // إرسال الدعوات المجدولة عند حلول موعد الإرسال send_date (يفحص كل دقيقة بدون تداخل)
        $schedule->command('events:send-scheduled-invitations')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/cron.log'));

        // تذكير المستخدمين بأحداث الغد - يعمل يومياً الساعة 4 عصراً (بتوقيت القاهرة والخليج)
        $schedule->command('events:remind-tomorrow')
            ->dailyAt('15:00')
            ->timezone('Africa/Cairo')
            ->appendOutputTo(storage_path('logs/cron.log'));

        // تذكير المستخدمين المجدول لهم موعد remeber_at عند حلول التاريخ والوقت - يفحص كل دقيقة بدون تداخل
        $schedule->command('events:remind-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/cron.log'));

        // تنظيف وحذف موظفي المسح المنتهية مناسباتهم - يعمل يومياً الساعة 8 صباحاً
        $schedule->command('scan-employees:clean')
            ->dailyAt('08:00')
            ->timezone('Africa/Cairo')
            ->appendOutputTo(storage_path('logs/cron.log'));

        // حذف رسائل الشات المؤقتة المنتهية الصلاحية كل 5 دقائق
        $schedule->command('user-chat:cleanup-expired')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/cron.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
