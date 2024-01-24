<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddPrinterToConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->string('PrinterNombre1')->nullable()->default('-')->after('fondo');
            $table->string('PrinterRuta1')->nullable()->default('-')->after('PrinterNombre1');
            $table->string('PrinterTipoConexion1')->nullable()->default('-')->after('PrinterRuta1');
            $table->string('PrinterNombre2')->nullable()->default('-')->after('PrinterTipoConexion1');
            $table->string('PrinterRuta2')->nullable()->default('-')->after('PrinterNombre2');
            $table->string('PrinterTipoConexion2')->nullable()->default('-')->after('PrinterRuta2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('PrinterNombre1');
            $table->dropColumn('PrinterRuta1');
            $table->dropColumn('PrinterTipoConexion1');
            $table->dropColumn('PrinterNombre2');
            $table->dropColumn('PrinterRuta2');
            $table->dropColumn('PrinterTipoConexion2');
        });
    }
}
