<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldOrden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_programaciones', function (Blueprint $table) {
            //
            $table->unsignedInteger('programacion_id')->nullable();
            $table->foreign('programacion_id')->references('id')->on('transporte_programaciones');
            $table->boolean('hidden')->default(false);
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_programaciones', function (Blueprint $table) {
            //
            $table->dropColumn('hidden');
            $table->dropForeign(['programacion_id']);
            $table->dropColumn('programacion_id');
        });
    }
}
