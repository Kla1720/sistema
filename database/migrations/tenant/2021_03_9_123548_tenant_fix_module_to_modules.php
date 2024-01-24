<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantFixModuleToModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            //haabilitar para actualizar si ya tiene datos, empresas, comentar luego para crear nuevos clientes o cuentas
//            DB::table('modules')->where('id', 15)->delete();//borrar hotel
//            DB::table('modules')->where('id', 16)->delete(); //borrar transporte
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            //
        });
    }
}
