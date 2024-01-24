<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantDropTransportePasajesDestinoIdForeignToTransportePasajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
            // para corregir decomentar esto y ejecutar, para nuevas empresas ya se corrige
//            $table->dropForeign('transporte_pasajes_destino_id_foreign');
//            $table->foreign('destino_id')->references('id')->on('transporte_destinos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
//            $table->foreign('destino_id')->references('id')->on('transporte_terminales');
        });
    }
}
