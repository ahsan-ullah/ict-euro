<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('installment_id')->index();
            $table->string('trx_number')->default(0);
            $table->string('method')->default('Stripe');
            $table->string('currency')->default('euro');
            $table->json('description')->nullable();
            $table->date('payment_date');
            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(0)->comment("Zero for pending, one for paid");
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('installment_id')->references('id')->on('payments');
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
};
