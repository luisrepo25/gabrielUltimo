<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('promociones')) {
            Schema::create('promociones', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100);
                $table->text('descripcion')->nullable();
                $table->enum('tipo', ['Global', 'Combo'])->default('Global');
                $table->decimal('descuento_porcentaje', 5, 2)->default(0);
                $table->decimal('precio_combo', 10, 2)->nullable();
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->enum('estado', ['Activo', 'Inactivo', 'Expirado'])->default('Activo');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promocion_productos')) {
            Schema::create('promocion_productos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('promocion_id');
                $table->integer('idproducto');

                $table->foreign('promocion_id')->references('id')->on('promociones')->onDelete('cascade');
                $table->foreign('idproducto')->references('idproducto')->on('producto');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('promocion_productos');
        Schema::dropIfExists('promociones');
    }
};
