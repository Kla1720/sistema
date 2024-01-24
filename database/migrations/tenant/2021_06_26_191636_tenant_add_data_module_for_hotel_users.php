<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddDataModuleForHotelUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::table('modules')->insert([
//            ['id'=> 15, 'value' => 'hotel', 'description' => 'Módulo hoteles', 'order_menu'=>14]
//        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        DB::table('modules')->where('id', 15)->delete();
    }
}
