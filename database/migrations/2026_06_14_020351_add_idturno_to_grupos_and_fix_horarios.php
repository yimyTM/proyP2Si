<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla pivot grupos ↔ horarios
        // El turno de un grupo se determina a través del horario asignado (horarios.idTurno)
        if (! Schema::hasTable('grupo__horarios')) {
            Schema::create('grupo__horarios', function (Blueprint $table) {
                $table->foreignId('codigoG')
                      ->constrained('grupos', 'codigoG')
                      ->onDelete('cascade');
                $table->foreignId('idHorario')
                      ->constrained('horarios', 'idHorario')
                      ->onDelete('cascade');
                $table->primary(['codigoG', 'idHorario']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo__horarios');
    }
};
