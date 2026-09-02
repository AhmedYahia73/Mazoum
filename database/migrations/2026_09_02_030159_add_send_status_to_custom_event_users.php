<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendStatusToCustomEventUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_event_users', function (Blueprint $table) {
            $table->enum('send_status', ['not_sent', 'sent', 'failed'])->default('not_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_event_users', function (Blueprint $table) {
            //
        });
    }
}
