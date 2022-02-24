<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingcartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shoppingcart', function (Blueprint $table) {
            $table->unsignedBigInteger('customerID');
            $table->foreign('customerID')->references('id')->on('users');
            $table->unsignedBigInteger('productID');
            $table->foreign('productID')->references('productID')->on('product');
            $table->integer('quantity');
            $table->primary(['customerID', 'productID']);
            //$table->float('subOrderAmount',,2);
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
        Schema::dropIfExists('shoppingcart');
    }
}
