<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchaseorder', function (Blueprint $table) {
            $table->id('poID');
            $table->unsignedBigInteger('customerID');
            $table->foreign('customerID')->references('id')->on('users');
            $table->date('purchase_date');
            $table->enum('status', array('pending', 'shipped', 'hold', 'cancelled'));
            $table->float('total_order_amount', 12, 2);
            $table->string('shipping_addr');
            $table->foreign('shipping_addr')->references('shipping_address')->on('users');
            $table->date('shipment_date')->nullable();
            $table->date('cancel_date')->nullable();
            $table->enum('cancel_by', array('customer', 'vendor'))->nullable();
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
        Schema::dropIfExists('purchaseorder');
    }
}
