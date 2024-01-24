<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAccountingPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting_plans', function (Blueprint $table) {

            $table->increments('id');
            $table->char('cuenta',12);
            $table->string('nombre');
            $table->integer('nivel');
            $table->char('tipo',1);
            $table->char('analisis',1);
            $table->string('currency_type_id');
            $table->boolean('balance_comprobacion')->default(false);
            $table->char('cuenta_equivalente',12)->nullable();
            $table->char('amarre_debe',12)->nullable();
            $table->char('amarre_haber',12)->nullable();
            $table->boolean('enabled')->default(true);

            $table->foreign('currency_type_id')->references('id')->on('cat_currency_types');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting_plans');
        //
    }
}
