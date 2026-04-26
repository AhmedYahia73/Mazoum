<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToCustomEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_event', function (Blueprint $table) {
            $table->integer("image_height")->default(300);
            $table->integer("image_width")->default(300);
            $table->string("text_color")->default("#000000");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_event', function (Blueprint $table) {
            //
        });
    }
}
