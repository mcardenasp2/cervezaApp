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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pulsera_id')->nullable()->constrained()->onDelete('set null');
            $table->string('codigo_uid');
            $table->foreignId('cerveza_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('mililitros_consumidos', 8, 2);
            $table->decimal('valor', 10, 2); // Agregando precisión
            $table->integer('estado')->default(1); // Corrección de comillas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
