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
        Schema::table('ventas_detalles', function (Blueprint $table) {
            $table->boolean('aplica_promocion')->default(false)->after('total');
            $table->boolean('producto_promocionado')->default(false)->after('aplica_promocion');
            $table->foreignId('promocion_id')
                ->nullable()
                ->constrained('promociones')
                ->onDelete('set null')
                ->after('producto_promocionado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas_detalles', function (Blueprint $table) {
            $table->dropForeign(['promocion_id']);
            $table->dropColumn(['aplica_promocion', 'producto_promocionado', 'promocion_id']);
        });
    }
};
