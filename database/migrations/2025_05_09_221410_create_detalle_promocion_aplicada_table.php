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
        Schema::create('detalle_promocion_aplicada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')
                ->nullable()
                ->constrained('ventas_encabezados')
                ->onDelete('set null');

            $table->foreignId('promocion_id')
                ->nullable()
                ->constrained('promociones')
                ->onDelete('set null');

            $table->foreignId('cerveza_id')
                ->nullable()
                ->constrained('cervezas')
                ->onDelete('set null');

            $table->float('cantidad_mililitros');
            $table->integer('cantidad_items_aplicados');
            $table->integer('cantidad_gratis');
            $table->float('total_descuento');
            $table->text('descripcion_snapshot');
            $table->integer('estado')->default(1)->comment('1: Activo, 0: Inactivo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_promocion_aplicada');
    }
};
