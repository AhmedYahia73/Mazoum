<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventVoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_voices', function (Blueprint $table) {
            $table->id();
            $table->string("voice");
            $table->foreignId("event_user_id")->constrained("event_users")->onDelete("cascade");
            $table->foreignId("custom_event_user_id")->constrained("custom_event_users")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_voices');
    }
}
