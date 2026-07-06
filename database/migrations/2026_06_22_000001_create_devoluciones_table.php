<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('devoluciones')) {
            Schema::create('devoluciones', function (Blueprint $table) {
                $table->id();
                $table->integer('nro_factura');
                $table->enum('tipo', ['Devolución', 'Garantía'])->default('Devolución');
                $table->text('motivo');
                $table->date('fecha');
                $table->enum('estado', ['Pendiente', 'Aprobado', 'Rechazado'])->default('Pendiente');
                $table->integer('ci_empleado')->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamps();

                $table->foreign('nro_factura')->references('nro')->on('NotaVenta');
                $table->foreign('ci_empleado')->references('ci')->on('usuario');
            });
        }

        if (!Schema::hasTable('devolucion_detalles')) {
            Schema::create('devolucion_detalles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('devolucion_id');
                $table->integer('idproducto');
                $table->integer('cantidad');
                $table->timestamps();

                $table->foreign('devolucion_id')->references('id')->on('devoluciones')->onDelete('cascade');
                $table->foreign('idproducto')->references('idproducto')->on('producto');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion_detalles');
        Schema::dropIfExists('devoluciones');
    }
};
