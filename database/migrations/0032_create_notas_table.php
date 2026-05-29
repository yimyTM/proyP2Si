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
        Schema::create('notas', function (Blueprint $table) {
            $table->id('idCalif');
            $table->decimal('calificacion', 5, 2);
            $table->foreignId('idInscripcion')
                  ->constrained('inscripcions', 'idInscripcion')
                  ->onDelete('cascade');
            $table->foreignId('idEx_materia')
                  ->constrained('exam_materias', 'idEx_materia')
                  ->onDelete('cascade');
            $table->unique(['idInscripcion', 'idEx_materia']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
