<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddForeignKeyIncomePaymentIdToCashDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_documents', function (Blueprint $table) {
            $table->foreign('income_payment_id')->references('id')->on('income')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop the keys
        Schema::table('cash_documents', function (Blueprint $table) {
            $table->dropForeign(['income_payment_id']);
        });
    }
}
