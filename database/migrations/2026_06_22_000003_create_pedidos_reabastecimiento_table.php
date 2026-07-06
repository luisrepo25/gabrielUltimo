<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedidos_reabastecimiento')) {
            Schema::create('pedidos_reabastecimiento', function (Blueprint $table) {
                $table->id();
                $table->integer('ci_empleado');
                $table->date('fecha');
                $table->enum('estado', ['Pendiente', 'Atendido', 'Cancelado'])->default('Pendiente');
                $table->text('observaciones')->nullable();
                $table->timestamps();

                $table->foreign('ci_empleado')->references('ci')->on('usuario');
            });
        }

        if (!Schema::hasTable('pedido_reabastecimiento_detalles')) {
            Schema::create('pedido_reabastecimiento_detalles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pedido_id');
                $table->integer('idproducto');
                $table->integer('cantidad_sugerida');
                $table->timestamps();

                $table->foreign('pedido_id')->references('id')->on('pedidos_reabastecimiento')->onDelete('cascade');
                $table->foreign('idproducto')->references('idproducto')->on('producto');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_reabastecimiento_detalles');
        Schema::dropIfExists('pedidos_reabastecimiento');
    }
};
