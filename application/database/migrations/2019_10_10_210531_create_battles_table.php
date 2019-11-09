<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBattlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_a');
            $table->unsignedBigInteger('user_b');
            $table->timestamps();

            $table->foreign('user_a')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('user_b')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battles');
    }
}
