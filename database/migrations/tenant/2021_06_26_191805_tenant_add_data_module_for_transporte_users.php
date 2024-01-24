<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDataModuleForTransporteUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::table('modules')->insert([
//            ['id'=> 16, 'value' => 'transporte', 'description' => 'Módulo transportes','order_menu'=>15]
//        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        DB::table('modules')->where('id', 16)->delete();
    }
}
