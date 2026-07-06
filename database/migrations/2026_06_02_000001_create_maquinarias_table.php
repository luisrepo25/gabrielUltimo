<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maquinarias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_hora', 10, 2);
            $table->decimal('precio_dia', 10, 2);
            $table->decimal('garantia_sugerida', 10, 2)->default(0.00);
            $table->enum('estado', ['disponible', 'alquilado', 'mantenimiento'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maquinarias');
    }
};
