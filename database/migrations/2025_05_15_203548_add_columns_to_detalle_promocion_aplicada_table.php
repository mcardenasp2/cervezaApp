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
        Schema::table('detalle_promocion_aplicada', function (Blueprint $table) {
            $table->dropForeign(['cerveza_id']);
            $table->dropColumn('cerveza_id');
        });

        Schema::table('detalle_promocion_aplicada', function (Blueprint $table) {
            $table->json('cervezas_ids')->nullable(); // o json si es mejor
            $table->integer('cantidad_promociones')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_promocion_aplicada', function (Blueprint $table) {
            $table->dropColumn('cervezas_ids');
            $table->dropColumn('cantidad_promociones');
        });
    }
};
