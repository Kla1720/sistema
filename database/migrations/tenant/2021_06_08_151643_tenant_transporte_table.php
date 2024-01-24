<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantTransporteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('transporte_estado_asientos')->insert([
            ['id' => 1, 'nombre' => 'Disponible'],
            ['id' => 2, 'nombre' => 'Ocupado'],
            ['id' => 3, 'nombre' => 'Reservado'],
            ['id' => 4, 'nombre' => 'Cancelado'],
        ]);

        DB::table('transporte_estado_encomienda')->insert([
            ['id' => 1, 'nombre' => 'En origen'],
            ['id' => 2, 'nombre' => 'En tránsito'],
            ['id' => 3, 'nombre' => 'En destino'],
            ['id' => 4, 'nombre' => 'Entregado'],
            ['id' => 5, 'nombre' => 'Cancelado']
        ]);

        DB::table('transporte_estado_pago_encomienda')->insert([
            ['id' => 1, 'nombre' => 'Pagado'],
            ['id' => 2, 'nombre' => 'pago en destino'],
        ]);



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transporte_estado_asientos');
        Schema::dropIfExists('transporte_estado_encomienda');
        Schema::dropIfExists('transporte_estado_pago_encomienda');

    }
}
