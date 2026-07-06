<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->string('usuario');    // Nombre o ID del usuario
            $table->string('accion');     // Insertar, Actualizar, Eliminar
            $table->string('tabla');      // En qué tabla ocurrió
            $table->integer('registro_id'); // ID del registro afectado
            $table->text('descripcion');  // Detalle de lo que cambió
            $table->ipAddress('ip');      // IP desde donde se hizo
            $table->timestamps();         // Fecha y hora (created_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
