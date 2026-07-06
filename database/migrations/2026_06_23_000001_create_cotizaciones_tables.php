<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->integer('ci_cliente');
            $table->date('fecha');
            $table->decimal('total', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('ci_cliente')->references('ci')->on('usuario')->onDelete('cascade');
        });

        Schema::create('cotizacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotizacion_id');
            $table->integer('idproducto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->timestamps();

            $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->onDelete('cascade');
            $table->foreign('idproducto')->references('idproducto')->on('producto')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_detalles');
        Schema::dropIfExists('cotizaciones');
    }
};
