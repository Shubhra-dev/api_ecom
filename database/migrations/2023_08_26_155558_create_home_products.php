<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1=Trending, 2=Best_Sellers 3=Top_rated 4=you may like');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('priority');
            $table->unsignedInteger('added_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('home_products');
    }
}
