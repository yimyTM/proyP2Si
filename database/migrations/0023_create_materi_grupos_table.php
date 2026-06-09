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
        Schema::create('materi_grupos', function (Blueprint $table) {
            $table->foreignId('codigoG')
                  ->constrained('grupos', 'codigoG')
                  ->onDelete('cascade');
            $table->foreignId('idMateria')
                  ->constrained('materias', 'idMateria')
                  ->onDelete('cascade');
            $table->foreignId('idHorario')
                  ->constrained('horarios', 'idHorario')
                  ->onDelete('cascade');
            $table->foreignId('idAula')
                  ->constrained('aulas', 'idAula')
                  ->onDelete('cascade')
                  ->after('idHorario');
            $table->foreignId('codigoDoc')
                  ->constrained('docentes', 'codigoDoc')
                  ->onDelete('cascade')
                  ->after('idAula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi_grupos');
    }
};
