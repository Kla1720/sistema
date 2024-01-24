<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddTipoComprobanteNoDomiciliadosToCatDocumentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('cat_document_types')->insert([
            ['id' => '00', 'active'=>1,'short'=>'OTR','description' => 'Otros'],
            ['id' => '91', 'active'=>1,'short'=>'CND', 'description' => 'Comprobante de No Domiciliado'],
            ['id' => '97', 'active'=>1,'short'=>'NCND', 'description' => 'Nota de Crédito - No Domiciliado'],
            ['id' => '98', 'active'=>1,'short'=>'NDND', 'description' => 'Nota de Débito - No Domiciliado'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('cat_document_types')->where('id', '00')->delete();
        DB::table('cat_document_types')->where('id', '91')->delete();
        DB::table('cat_document_types')->where('id', '97')->delete();
        DB::table('cat_document_type')->where('id', '98')->delete();

    }
}
