<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductreviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productreview', function (Blueprint $table) {
            $table->unsignedBigInteger('productID');
            $table->foreign('productID')->references('productID')->on('product');
            $table->unsignedBigInteger('poID');
            $table->foreign('poID')->references('poID')->on('purchaseorder');
            $table->integer('rating');
            $table->string('review');
            $table->primary(['productID', 'poID']);
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
        Schema::dropIfExists('productreview');
    }
}
