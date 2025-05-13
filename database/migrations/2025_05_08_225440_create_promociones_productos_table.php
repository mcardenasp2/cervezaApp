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
        Schema::create('promociones_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocion_id')
            ->nullable()
            ->constrained('promociones') // aquí indicas el nombre exacto de la tabla
            ->onDelete('set null');

            $table->foreignId('cerveza_id')
                ->nullable()
                ->constrained('cervezas')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones_productos');

    }
};
