<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCustomEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_event', function (Blueprint $table) {
            $table->boolean("name_qr")->default(0);
            $table->boolean("number_qr")->default(0);
            $table->integer("qr_height")->default(300);
            $table->integer("qr_width")->default(300);
            $table->integer("qr_x")->default(10);
            $table->integer("qr_y")->default(10);
            $table->decimal("lat", 10, 2)->default(0);
            $table->decimal("lng", 10, 2)->default(0);
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
