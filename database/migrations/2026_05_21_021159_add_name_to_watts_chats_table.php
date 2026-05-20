<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToWattsChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watts_chats', function (Blueprint $table) {
            $table->string('name')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('watts_chats', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
