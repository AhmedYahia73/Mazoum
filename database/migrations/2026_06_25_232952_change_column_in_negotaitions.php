<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnInNegotaitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('negotaitions', function (Blueprint $table) {
            $table->enum("status", ["pending", "inprogress", "approve", "reject"])->default("pending")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('negotaitions', function (Blueprint $table) {
            //
        });
    }
}
