<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeCustomerIdToDispatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('dispatches', function (Blueprint $table) {
//            $table->unsignedInteger('customer_id')->nullable()->change();
//            $table->json('customer')->nullable()->change();
//            $table->string('transport_mode_type_id')->nullable()->change();
//            $table->string('transfer_reason_type_id')->nullable()->change();
//            $table->json('origin')->nullable()->change();
//            $table->json('delivery')->nullable()->change();
//
//        });

        Schema::table('dispatches', function (Blueprint $table) {
            DB::statement('ALTER TABLE `dispatches` CHANGE `customer_id` `customer_id` INT(10) UNSIGNED NULL;');
            DB::statement('ALTER TABLE `dispatches` CHANGE `customer` `customer` JSON NULL;');
            DB::statement('ALTER TABLE `dispatches` CHANGE `transport_mode_type_id` `transport_mode_type_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;');
            DB::statement('ALTER TABLE `dispatches` CHANGE `transfer_reason_type_id` `transfer_reason_type_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;');
            DB::statement('ALTER TABLE `dispatches` CHANGE `origin` `origin` JSON NULL;');
            DB::statement('ALTER TABLE `dispatches` CHANGE `delivery` `delivery` JSON NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
