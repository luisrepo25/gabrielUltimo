<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mantenimientos')) {
            Schema::create('mantenimientos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('maquinaria_id');
                $table->enum('tipo', ['Preventivo', 'Correctivo'])->default('Preventivo');
                $table->text('descripcion');
                $table->decimal('costo', 10, 2)->default(0);
                $table->date('fecha_inicio');
                $table->date('fecha_fin')->nullable();
                $table->enum('estado', ['Programado', 'En curso', 'Finalizado'])->default('Programado');
                $table->integer('ci_responsable')->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamps();

                $table->foreign('maquinaria_id')->references('id')->on('maquinarias')->onDelete('cascade');
                $table->foreign('ci_responsable')->references('ci')->on('usuario');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
