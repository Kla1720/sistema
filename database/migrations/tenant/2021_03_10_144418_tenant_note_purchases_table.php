<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantNotePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('purchase_id');
            $table->enum('note_type', ['credit', 'debit']);
            $table->string('note_credit_type_id')->nullable();
            $table->string('note_debit_type_id')->nullable();
            $table->string('note_description');
            $table->unsignedInteger('affected_purchase_id');

            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('note_credit_type_id')->references('id')->on('cat_note_credit_types');
            $table->foreign('note_debit_type_id')->references('id')->on('cat_note_debit_types');
            $table->foreign('affected_purchase_id')->references('id')->on('purchases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_purchases');
    }
}
