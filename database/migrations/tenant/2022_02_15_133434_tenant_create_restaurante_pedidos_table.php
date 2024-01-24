<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TenantCreateRestaurantePedidosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurante_pedidos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('mesa_id')->nullable();
            $table->unsignedInteger('document_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->enum('tipo', ['mesa', 'delivery', 'llevar'])->default('mesa');
            $table->boolean('estado')->default(true);
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
        Schema::dropIfExists('restaurante_pedidos');

    }
}
