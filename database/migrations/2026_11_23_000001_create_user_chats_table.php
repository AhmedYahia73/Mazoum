<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->boolean('is_admin')->default(false)->comment('false: sent by user, true: sent by admin/employee');
            $table->text('msg')->nullable();
            $table->boolean('is_admin_read')->default(false);
            $table->boolean('is_user_read')->default(false);
            $table->string('image')->nullable();
            $table->string('voice')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_admin']);
            $table->index(['user_id', 'is_admin_read']);
            $table->index(['user_id', 'is_user_read']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_chats');
    }
}
