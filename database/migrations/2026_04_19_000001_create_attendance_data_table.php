<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceDataTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_data', function (Blueprint $table) {
            $table->id();
            $table->string('router_ip')->nullable();
            // Array of location points (lat/lng) defining the perimeter
            // e.g. [{"lat": 29.123, "lng": 47.456}, ...]
            $table->json('locations')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_data');
    }
}
