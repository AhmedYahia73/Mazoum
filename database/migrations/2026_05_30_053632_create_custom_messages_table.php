<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_event_id')->nullable()->constrained('custom_event')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('custom_user_id')->nullable()->constrained('custom_event_users')->onUpdate('cascade')->onDelete('cascade');
            $table->string("msg");
            $table->enum("type", ["congratulation", "apologize"]);
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
        Schema::dropIfExists('custom_messages');
    }
}
