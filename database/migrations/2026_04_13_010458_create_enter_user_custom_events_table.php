<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterUserCustomEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enter_user_custom_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_user_id')->nullable()->constrained("custom_event_users")->onUpdate('cascade')->onDelete('cascade');
            $table->integer("count");
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
        Schema::dropIfExists('enter_user_custom_events');
    }
}
