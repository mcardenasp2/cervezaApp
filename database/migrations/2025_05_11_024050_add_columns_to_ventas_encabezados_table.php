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
            $table->float('descuento');
            $table->float('total_pagar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas_encabezados', function (Blueprint $table) {
            $table->dropColumn(['descuento', 'total_pagar']);
        });
    }
};
