<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantTransporteConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporte_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('pasaje_afecto_igv')->default(0);
            $table->boolean('encomienda_afecto_igv')->default(1);
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('transporte_configurations')->insert(
            array(
                'pasaje_afecto_igv' => 0,
                'encomienda_afecto_igv' => 1
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transporte_configurations');
    }
}
