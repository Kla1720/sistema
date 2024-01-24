<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantOptionalDestinatarioIdToTransporteEncomiendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            $table->unsignedInteger('destinatario_id')->nullable(true)->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            //
        });
    }
}
