<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWattsChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watts_chats', function (Blueprint $table) {
            $table->id();
        // '',
        // '',
        // '',
        // ''
            $table->string('phone')->nullable();
            $table->text('message')->nullable(); 
            $table->string('message_id')->nullable(); 
            $table->boolean('is_sent_by_me')->nullable(); 
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
        Schema::dropIfExists('watts_chats');
    }
}
