<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantNoteIdToTransportePasajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
            $table->unsignedInteger('document_id')->nullable()->change();
            $table->unsignedInteger('note_id')->nullable()->after('document_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_pasajes', function (Blueprint $table) {
            //
        });
    }
}
