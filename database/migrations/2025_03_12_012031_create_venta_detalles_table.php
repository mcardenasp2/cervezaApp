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
        Schema::create('ventas_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabecera_id')->nullable()->constrained('ventas_encabezados')->onDelete('set null');
            $table->foreignId('cerveza_id')->nullable()->constrained('cervezas')->onDelete('set null');
            $table->decimal('mililitros_consumidos', 8, 2);
            $table->decimal('precio_por_mililitro', 10, 2);
            $table->decimal('total', 10, 2);
            $table->integer('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_detalles');
    }
};
