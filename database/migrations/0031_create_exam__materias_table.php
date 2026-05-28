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
        Schema::create('exam_materias', function (Blueprint $table) {
            $table->id('idEx_materia');
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->foreignId('idExamen')
                  ->constrained('examenes', 'idExamen')
                  ->onDelete('cascade');
            $table->foreignId('idMateria')
                  ->constrained('materias', 'idMateria')
                  ->onDelete('cascade');
            $table->unique(['idExamen', 'idMateria']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_materias');
    }
};
