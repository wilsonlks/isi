<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductpropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productproperty', function (Blueprint $table) {
            $table->unsignedBigInteger('productID');
            $table->foreign('productID')->references('productID')->on('product');
            $table->unsignedBigInteger('property_number');
            $table->string('detail_description');
            $table->primary(['productID', 'property_number']);
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
        Schema::dropIfExists('productproperty');
    }
}
