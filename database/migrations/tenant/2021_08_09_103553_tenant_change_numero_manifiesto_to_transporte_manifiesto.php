<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantChangeNumeroManifiestoToTransporteManifiesto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transporte_manifiesto', function (Blueprint $table) {
            DB::statement("ALTER TABLE transporte_manifiesto MODIFY numero integer not null;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transporte_manifiesto', function (Blueprint $table) {
            $table->string('numero')->nullable(false)->collation('utf8mb4_unicode_ci')->change();
        });
    }
}
