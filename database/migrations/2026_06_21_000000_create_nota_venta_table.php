<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('NotaVenta', function (Blueprint $table) {
            $table->integer('nro')->primary();
            $table->dateTime('fecha')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->integer('ci_cliente')->nullable();
            $table->integer('ci_empleado')->nullable();
            $table->integer('id_pago')->nullable();
            $table->timestamps();
        });
        
        Schema::create('detalleNotaVenta', function (Blueprint $table) {
            $table->integer('nro_factura');
            $table->integer('id_producto');
            $table->decimal('precio_unitario', 10, 2)->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('descuento', 10, 2)->nullable();
            $table->primary(['nro_factura', 'id_producto']);
            $table->foreign('nro_factura')->references('nro')->on('NotaVenta');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('detalleNotaVenta');
        Schema::dropIfExists('NotaVenta');
    }
};
