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
        if (!Schema::hasTable('proveedor')) {
            Schema::create('proveedor', function (Blueprint $table) {
                $table->integer('ci')->primary();
                $table->string('nombre', 100);
                $table->text('descripcion');
                $table->integer('telefono');
                $table->string('correo', 50)->nullable();
                $table->string('direccion', 100)->nullable();
            });
        }

        if (!Schema::hasTable('NotaCompra')) {
            Schema::create('NotaCompra', function (Blueprint $table) {
                $table->integer('nro')->primary();
                $table->dateTime('fecha')->useCurrent();
                $table->decimal('total', 10, 2)->default(0);
                $table->integer('ci_proveedor')->nullable();
                $table->integer('id_pago')->nullable();

                $table->foreign('ci_proveedor')->references('ci')->on('proveedor')->onUpdate('cascade')->onDelete('no action');
                $table->foreign('id_pago')->references('id')->on('metodoPago')->onUpdate('cascade')->onDelete('no action');
            });
        }

        if (!Schema::hasTable('detalleNotaCompra')) {
            Schema::create('detalleNotaCompra', function (Blueprint $table) {
                $table->integer('nro_factura');
                $table->integer('id_producto');
                $table->decimal('precio_unitario', 10, 2);
                $table->integer('cantidad');

                $table->primary(['nro_factura', 'id_producto']);
                $table->foreign('nro_factura')->references('nro')->on('NotaCompra')->onUpdate('cascade')->onDelete('no action');
                $table->foreign('id_producto')->references('idproducto')->on('producto')->onUpdate('cascade')->onDelete('no action');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalleNotaCompra');
        Schema::dropIfExists('NotaCompra');
        Schema::dropIfExists('proveedor');
    }
};
