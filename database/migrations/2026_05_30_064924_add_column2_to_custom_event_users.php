<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumn2ToCustomEventUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_event_users', function (Blueprint $table) {
            $table->integer("confirm_count")->default(0);
            $table->integer("apologize_count")->default(0);
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
