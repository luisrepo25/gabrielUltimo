<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquileres', function (Blueprint $table) {
            $table->id();
            $table->integer('ci_cliente');
            $table->integer('ci_empleado');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin_estimada');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->string('garantizado_con', 255)->nullable();
            $table->decimal('monto_garantia', 10, 2)->default(0.00);
            $table->decimal('total_estimado', 10, 2);
            $table->decimal('total_real', 10, 2)->nullable();
            $table->enum('estado', ['activo', 'completado', 'cancelado', 'atrasado'])->default('activo');
            $table->integer('metodo_pago_id');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ci_cliente')->references('ci')->on('usuario')->onDelete('restrict');
            $table->foreign('ci_empleado')->references('ci')->on('usuario')->onDelete('restrict');
            $table->foreign('metodo_pago_id')->references('id')->on('metodoPago')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquileres');
    }
};
