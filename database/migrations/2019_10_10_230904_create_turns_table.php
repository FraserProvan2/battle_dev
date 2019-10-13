<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('battle_id');
            $table->string('status')->default('pending');
            $table->integer('turn_number')->default(1);
            $table->enum('player_a_action', ['attack', 'block'])->nullable();
            $table->enum('player_b_action', ['attack', 'block'])->nullable();
            $table->longText('battle_frame')->nullable();
            $table->timestamps();

            $table->foreign('battle_id')
                ->references('id')->on('battles')
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
        Schema::dropIfExists('turns');
    }
}
