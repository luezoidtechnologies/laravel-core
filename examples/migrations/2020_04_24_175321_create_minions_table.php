<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->unsignedSmallInteger('total_eyes');
            $table->string('favourite_sound', 255);
            $table->boolean('has_hairs')->default(false);
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
        Schema::table('minions', function (Blueprint $table) {
            $table->drop();
        });
    }
}
