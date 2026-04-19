<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryAppointmentToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('salary', 10, 2)->nullable()->after('user_type');
            $table->time('appointment_from')->nullable()->after('salary');
            $table->time('appointment_to')->nullable()->after('appointment_from');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary', 'appointment_from', 'appointment_to']);
        });
    }
}
