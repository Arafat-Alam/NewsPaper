<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewsWiseImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_wise_image', function(Blueprint $table){
            $table->increments('id');
            $table->integer('fk_post_id');
            $table->string('title_lang1');
            $table->string('title_lang2');
            $table->string('image_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('news_wise_image');
    }
}
