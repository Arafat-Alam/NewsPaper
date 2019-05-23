<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewsCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_category', function(Blueprint $table){
            $table->increments('id');
            $table->string('category_name_lang1');
            $table->string('category_name_lang2');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('news_category');    
    }
}
