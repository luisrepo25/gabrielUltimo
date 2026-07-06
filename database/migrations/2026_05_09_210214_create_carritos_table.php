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
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();
            $table->integer('ci_usuario');
            $table->integer('idproducto');
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('ci_usuario')->references('ci')->on('usuario')->onDelete('cascade');
            $table->foreign('idproducto')->references('idproducto')->on('producto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carritos');
    }
};
