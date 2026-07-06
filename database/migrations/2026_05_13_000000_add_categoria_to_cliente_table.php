<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('cliente', 'categoria')) {
                $table->string('categoria', 50)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cliente', function (Blueprint $table) {
            if (Schema::hasColumn('cliente', 'categoria')) {
                $table->dropColumn('categoria');
            }
        });
    }
};
