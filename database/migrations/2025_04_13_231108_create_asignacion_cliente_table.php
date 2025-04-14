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
        Schema::create('asignacion_pulseras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('pulsera_id')->nullable()->constrained('pulseras')->onDelete('set null');
            $table->date('fecha_creacion')->nullable()->default(now());
            $table->timestamp('fecha_inicio_asignacion')->nullable()->default(now());
            $table->timestamp('fecha_fin_asignacion')->nullable();
            $table->integer('estado')->default(1)->comment('1: Iniciado, 0: Inactivo', '2: Finalizado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_pulseras');
    }
};
