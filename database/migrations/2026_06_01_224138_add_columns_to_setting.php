<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting', function (Blueprint $table) { 
            $table->string("kw_access_token2")->nullable();
            $table->string("kw_phone_numer_id2")->nullable();
            $table->string("kw_sender_id2")->nullable();
            $table->string("sa_access_token2")->nullable();
            $table->string("sa_phone_numer_id2")->nullable();
            $table->string("sa_sender_id2")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting', function (Blueprint $table) {
            //
        });
    }
}
