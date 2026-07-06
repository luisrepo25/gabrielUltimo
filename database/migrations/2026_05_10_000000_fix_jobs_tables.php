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
        // 1. (Omitido) El usuario maneja el ID de 'rol' manualmente en el código.

        // 2. Añadir la columna faltante en 'estadoRol'
        if (Schema::hasTable('estadoRol')) {
            Schema::table('estadoRol', function (Blueprint $table) {
                if (!Schema::hasColumn('estadoRol', 'fechaFin')) {
                    $table->date('fechaFin')->nullable()->after('fechaInicio');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: revertir cambios
    }
};
