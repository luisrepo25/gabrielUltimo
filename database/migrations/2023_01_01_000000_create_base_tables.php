<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. MARCA
        if (!Schema::hasTable('marca')) {
            Schema::create('marca', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('nombre', 50);
            });
        }

        // 2. COLOR
        if (!Schema::hasTable('color')) {
            Schema::create('color', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('nombre', 50);
            });
        }

        // 3. MEDIDA
        if (!Schema::hasTable('medida')) {
            Schema::create('medida', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('longitud', 20)->nullable();
                $table->string('ancho', 20)->nullable();
                $table->string('alto', 20)->nullable();
            });
        }

        // 4. VOLUMEN
        if (!Schema::hasTable('volumen')) {
            Schema::create('volumen', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('peso', 20)->nullable();
                $table->string('volumen_m3', 20)->nullable();
            });
        }

        // 5. METODO DE PAGO
        if (!Schema::hasTable('metodoPago')) {
            Schema::create('metodoPago', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('nombre', 50);
            });
        }

        // 6. CATEGORIA
        if (!Schema::hasTable('categoria')) {
            Schema::create('categoria', function (Blueprint $table) {
                $table->integer('idcategoria')->primary();
                $table->string('nombre', 50);
                $table->text('descripcion');
                $table->integer('id_categoria_padre')->nullable();
                $table->foreign('id_categoria_padre')->references('idcategoria')->on('categoria');
            });
        }

        // 7. USUARIO (Estructura base)
        if (!Schema::hasTable('usuario')) {
            Schema::create('usuario', function (Blueprint $table) {
                $table->integer('ci')->primary();
                $table->string('nombre', 50);
                $table->string('apellido', 50);
                $table->integer('telefono')->nullable();
                $table->char('sexo', 1)->nullable();
                $table->string('correo', 50); // Se renombrará a email luego
                $table->string('domicilio', 50)->nullable();
                $table->string('tipoPersona', 10);
            });
        }

        // 7. PRODUCTO
        if (!Schema::hasTable('producto')) {
            Schema::create('producto', function (Blueprint $table) {
                $table->integer('idproducto')->primary();
                $table->string('nombre', 50);
                $table->text('descripcion')->nullable();
                $table->decimal('precio', 10, 2);
                $table->integer('cantidad');
                $table->date('fechacaducidad')->nullable();
                $table->integer('id_marca');
                $table->integer('id_categoria');
                $table->integer('id_color')->nullable();
                $table->integer('id_medida')->nullable();
                $table->integer('id_volumen')->nullable();

                $table->foreign('id_marca')->references('id')->on('marca');
                $table->foreign('id_categoria')->references('idcategoria')->on('categoria');
                $table->foreign('id_color')->references('id')->on('color');
                $table->foreign('id_medida')->references('id')->on('medida');
                $table->foreign('id_volumen')->references('id')->on('volumen');
            });
        }

        // 9. EMPLEADO (Herencia de Usuario)
        if (!Schema::hasTable('empleado')) {
            Schema::create('empleado', function (Blueprint $table) {
                $table->integer('ci')->primary();
                $table->decimal('salario', 10, 2);
                $table->string('estado', 10);
                $table->foreign('ci')->references('ci')->on('usuario')->onDelete('cascade');
            });
        }

        // 10. CLIENTE (Herencia de Usuario)
        if (!Schema::hasTable('cliente')) {
            Schema::create('cliente', function (Blueprint $table) {
                $table->integer('ci')->primary();
                $table->integer('puntos')->default(0);
                $table->foreign('ci')->references('ci')->on('usuario')->onDelete('cascade');
            });
        }

        // 11. ROL
        if (!Schema::hasTable('rol')) {
            Schema::create('rol', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->string('nombre', 50);
                $table->text('descripcion');
            });
        }

        // 10. ESTADOROL
        if (!Schema::hasTable('estadoRol')) {
            Schema::create('estadoRol', function (Blueprint $table) {
                $table->integer('id_rol');
                $table->integer('ci_empleado');
                $table->date('fechaInicio');
                $table->date('fechaFin')->nullable();
                $table->string('estado', 10);
                $table->primary(['id_rol', 'ci_empleado']);
                $table->foreign('id_rol')->references('id')->on('rol');
                $table->foreign('ci_empleado')->references('ci')->on('empleado');
            });
        }

        // 11. BITACORA
        if (!Schema::hasTable('bitacora')) {
            Schema::create('bitacora', function (Blueprint $table) {
                $table->id();
                $table->string('accion');
                $table->string('tabla');
                $table->string('registro_id');
                $table->text('descripcion');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
        Schema::dropIfExists('estadoRol');
        Schema::dropIfExists('rol');
        Schema::dropIfExists('empleado');
        Schema::dropIfExists('producto');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('categoria');
        Schema::dropIfExists('volumen');
        Schema::dropIfExists('medida');
        Schema::dropIfExists('color');
        Schema::dropIfExists('marca');
    }
};
