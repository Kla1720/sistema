<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddModulesToModuleLevelUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_level_user', function (Blueprint $table) {
            //
        });
        DB::table('module_level_user')->insert([

            ['module_level_id' => 10, 'user_id' => 1],
            ['module_level_id' => 11, 'user_id' => 1],
            ['module_level_id' => 12, 'user_id' => 1],
            ['module_level_id' => 13, 'user_id' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('module_level_user')->whereIn('module_level_id', [10, 11, 12, 13])->delete();
    }
}
