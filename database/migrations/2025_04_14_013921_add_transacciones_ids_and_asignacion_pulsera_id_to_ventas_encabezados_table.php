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
        Schema::table('ventas_encabezados', function (Blueprint $table) {
            $table->string('transacciones_ids')->nullable();
            $table->foreignId('asignacion_pulsera_id')
                ->nullable()
                ->constrained('asignacion_pulseras')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas_encabezados', function (Blueprint $table) {
            $table->dropColumn('transacciones_ids');
            $table->dropForeign(['asignacion_pulsera_id']);
            $table->dropColumn('asignacion_pulsera_id');
        });
    }
};
