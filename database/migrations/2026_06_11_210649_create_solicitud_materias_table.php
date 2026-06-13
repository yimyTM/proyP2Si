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
        Schema::create('solicitud_materias', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente');
            $table->foreignId('codigoDoc')
                  ->constrained('docentes', 'codigoDoc')
                  ->onDelete('cascade');
            $table->foreignId('idMateria')
                  ->constrained('materias', 'idMateria')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_materias');
    }
};
