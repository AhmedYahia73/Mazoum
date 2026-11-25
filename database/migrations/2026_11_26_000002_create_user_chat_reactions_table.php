<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserChatReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_chat_reactions', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('user_chat_id')->constrained('user_chats')->onDelete('cascade');
            $table->string('actor_type', 20)->comment('user | admin | employee');
            $table->unsignedBigInteger('actor_id');
            $table->string('emoji', 32);
            $table->timestamps();

            $table->unique(['user_chat_id', 'actor_type', 'actor_id'], 'ucr_msg_actor_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_chat_reactions');
    }
}
