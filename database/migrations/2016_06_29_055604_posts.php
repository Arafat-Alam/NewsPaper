<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Posts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function(Blueprint $table){
            $table->increments('id');
            $table->string('title_lang1');
            $table->string('title_lang2');
            $table->string('description_lang1');
            $table->string('description_lang2');
            $table->integer('total_views');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('fk_category_id');
            $table->integer('fk_sub_category_id');
            $table->integer('fk_division_id');
            $table->string('post');
            $table->string('slug');
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
        Schema::Drop('posts');    
    }
}
