<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(2125);
$records = \App\Models\Attendance::where('user_id', 2125)->whereDate('from', '2026-08-05')->get();

echo "USER_APPOINTMENT_FROM: " . $user->appointment_from . "\n";
echo "USER_APPOINTMENT_TO: " . $user->appointment_to . "\n";

foreach ($records as $r) {
    echo "RECORD_ID: " . $r->id . "\n";
    echo "FROM: " . $r->from . "\n";
    echo "TO: " . $r->to . "\n";
}
