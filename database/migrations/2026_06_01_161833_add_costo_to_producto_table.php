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
        // Verifica si la columna NO existe en la base remota para evitar errores si ya existe localmente
        if (!Schema::hasColumn('producto', 'costo')) {
            Schema::table('producto', function (Blueprint $table) {
                $table->decimal('costo', 10, 2)->nullable()->after('precio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('producto', 'costo')) {
            Schema::table('producto', function (Blueprint $table) {
                $table->dropColumn('costo');
            });
        }
    }
};
