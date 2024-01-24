<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantAddIsCustomTicketToFormatTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('format_templates')
            ->where('formats', 'aqpfact_01')
            ->orWhere('formats', 'aqpfact_02')
            ->orWhere('formats', 'admiconta')
            ->update(['is_custom_ticket' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('format_templates')
            ->where('formats', 'aqpfact_01')
            ->orWhere('formats', 'aqpfact_02')
            ->orWhere('formats', 'admiconta')
            ->update(['is_custom_ticket' => 0]);
    }
}
