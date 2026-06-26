<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumn2ToWattsChats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watts_chats', function (Blueprint $table) {
            $table->foreignId('event_user_id')->nullable()->constrained("event_users")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained("events")->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watts_chats', function (Blueprint $table) {
            //
        });
    }
}
