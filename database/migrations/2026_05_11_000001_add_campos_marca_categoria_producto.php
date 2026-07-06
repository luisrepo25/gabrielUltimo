<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marca', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('nombre');
            $table->boolean('estado')->default(true)->after('logo');
        });

        Schema::table('categoria', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('descripcion');
            $table->boolean('estado')->default(true)->after('imagen');
        });

        Schema::table('producto', function (Blueprint $table) {
            $table->string('imagen')->nullable()->after('descripcion');
            $table->string('modelo', 100)->nullable()->after('imagen');
        });
    }

    public function down(): void
    {
        Schema::table('marca', function (Blueprint $table) {
            $table->dropColumn(['logo', 'estado']);
        });

        Schema::table('categoria', function (Blueprint $table) {
            $table->dropColumn(['imagen', 'estado']);
        });

        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn(['imagen', 'modelo']);
        });
    }
};
