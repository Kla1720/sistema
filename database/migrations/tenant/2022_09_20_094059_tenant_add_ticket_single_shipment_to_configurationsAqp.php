<?php
use Illuminate\Database\Migrations\Migration;

class TenantAddTicketSingleShipmentToConfigurationsAqp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('configurations')->update(['ticket_single_shipment' => 1]);
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
