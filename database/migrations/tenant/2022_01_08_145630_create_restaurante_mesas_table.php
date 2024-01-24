<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestauranteMesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurante_mesas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numero');
            $table->unsignedBigInteger('nivel_id');
            $table->boolean('activo');


            $table->foreign('nivel_id')->references('id')->on('restaurante_niveles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurante_mesas');
    }
}
