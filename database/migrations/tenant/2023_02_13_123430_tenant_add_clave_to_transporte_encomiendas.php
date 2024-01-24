<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddClaveToTransporteEncomiendas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_encomiendas', function (Blueprint $table) {
            $table->integer('clave')->nullable()->after('destinatario_nombre');
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
            $table->dropColumn('clave');
        });
    }
}
