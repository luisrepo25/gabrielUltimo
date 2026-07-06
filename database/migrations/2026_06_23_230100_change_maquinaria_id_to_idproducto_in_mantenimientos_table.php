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
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropForeign(['maquinaria_id']);
            $table->dropColumn('maquinaria_id');
            $table->integer('idproducto')->after('id');
            $table->foreign('idproducto')->references('idproducto')->on('producto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->dropForeign(['idproducto']);
            $table->dropColumn('idproducto');
            $table->unsignedBigInteger('maquinaria_id')->after('id');
            $table->foreign('maquinaria_id')->references('id')->on('maquinarias')->onDelete('cascade');
        });
    }
};
