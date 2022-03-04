<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseorderdetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchaseorderdetail', function (Blueprint $table) {
            $table->unsignedBigInteger('poID');
            $table->foreign('poID')->references('poID')->on('purchaseorder');
            $table->unsignedBigInteger('productID');
            $table->foreign('productID')->references('productID')->on('product');
            $table->float('price', 10, 2);
            $table->integer('quantity');
            $table->float('sub_order_amount', 12, 2);
            $table->primary(['poID', 'productID']);
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
        Schema::dropIfExists('purchaseorderdetail');
    }
}
