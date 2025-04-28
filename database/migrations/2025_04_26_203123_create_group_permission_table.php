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
        Schema::create('group_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                  ->nullable()  // Permitir nulos para que funcione `onDelete('set null')`
                  ->constrained()
                  ->onDelete('set null');  // Relación con el grupo, y set null cuando se elimina el grupo
            $table->foreignId('permission_id')
                  ->nullable()  // Permitir nulos para que funcione `onDelete('set null')`
                  ->constrained()
                  ->onDelete('set null');  // Relación con el permiso, y set null cuando se elimina el permiso
            $table->integer('estado')->default(1); // Estado del permiso (1 = activo, 0 = inactivo)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_permission');
    }
};
