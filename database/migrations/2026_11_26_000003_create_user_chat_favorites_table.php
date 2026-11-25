<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserChatFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_chat_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_chat_id')->constrained('user_chats')->onDelete('cascade');
            $table->string('actor_type', 20)->comment('user | admin | employee');
            $table->unsignedBigInteger('actor_id');
            $table->timestamps();

            $table->unique(['user_chat_id', 'actor_type', 'actor_id'], 'ucf_msg_actor_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_chat_favorites');
    }
}
