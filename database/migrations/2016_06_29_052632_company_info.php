<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompanyInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_info', function(Blueprint $table){
            $table->increments('id');
            $table->string('company_name');
            $table->string('logo');
            $table->string('estd');
            $table->string('address');
            $table->string('mobile_no');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('company_info');
    }
}    
