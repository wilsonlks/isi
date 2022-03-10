<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductreviewTableWithNewreviewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productreview', function (Blueprint $table) {
            
            $table->dateTime('review_date');
            $table->integer('rating_new')->nullable();
            $table->string('review_new')->nullable();
            $table->dateTime('review_date_new')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productreview', function (Blueprint $table) {
            
            $table->dropColumn(['review_date', 'rating_new', 'review_new', 'review_date_new']);

        });
    }
}
