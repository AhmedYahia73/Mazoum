<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomMemoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_memories', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->foreignId('custom_user_id')->nullable()->constrained("custom_event_users")->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('custom_event_id')->nullable()->constrained("custom_event")->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('custom_memories');
    }
}
