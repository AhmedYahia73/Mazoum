<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_chats', function (Blueprint $table) {
            $table->id();
            $table->string("msg")->nullable();
            $table->string("image")->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('event_user_id')->nullable()->constrained("event_users")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained("events")->onUpdate('cascade')->onDelete('cascade');
            $table->boolean("user_sent");
            $table->boolean("is_read")->default(0);
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
        Schema::dropIfExists('event_chats');
    }
}
