<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrColumnsToEvents extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('send_type')->nullable()->after('color');
            $table->boolean('name_qr')->default(false)->after('send_type');
            $table->boolean('number_qr')->default(false)->after('name_qr');
            $table->integer('qr_height')->nullable()->after('number_qr');
            $table->integer('qr_width')->nullable()->after('qr_height');
            $table->integer('qr_x')->nullable()->after('qr_width');
            $table->integer('qr_y')->nullable()->after('qr_x');
            $table->boolean('resend_qr')->default(false)->after('qr_y');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['send_type','name_qr','number_qr','qr_height','qr_width','qr_x','qr_y','resend_qr']);
        });
    }
}
