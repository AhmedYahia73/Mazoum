<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_settings', function (Blueprint $table) {
            $table->id(); 
            $table->text('phone_numer_id')->nullable();
            $table->text('sender_id')->nullable(); 
            $table->foreignId('country_id')->nullable()->constrained("countries")->onUpdate('cascade')->onDelete('set null');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('new_settings');
    }
}
