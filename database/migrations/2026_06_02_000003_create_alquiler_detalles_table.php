<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alquiler_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alquiler_id');
            $table->unsignedBigInteger('maquinaria_id');
            $table->decimal('precio_unitario', 10, 2);
            $table->enum('tipo_tarifa', ['hora', 'dia']);
            $table->integer('tiempo_rentado'); // número de horas o días
            $table->timestamps();

            $table->foreign('alquiler_id')->references('id')->on('alquileres')->onDelete('cascade');
            $table->foreign('maquinaria_id')->references('id')->on('maquinarias')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alquiler_detalles');
    }
};
