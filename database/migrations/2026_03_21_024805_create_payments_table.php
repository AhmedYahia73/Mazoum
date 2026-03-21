<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('currency_id')->nullable()->constrained("currency")->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->string("receipt")->nullable();
            $table->decimal("price", 10, 2);
            $table->enum("status", ["pending", "approve", "reject"])->default("pending");
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
        Schema::dropIfExists('payments');
    }
}
