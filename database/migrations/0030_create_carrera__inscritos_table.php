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
        Schema::create('carrera__inscritos', function (Blueprint $table) {
            $table->id('idCarreraInscrito');
            $table->integer('prioridad')->nullable();
            $table->foreignId('idInscripcion')
                  ->constrained('inscripciones', 'idInscripcion')
                  ->onDelete('cascade');
            $table->foreignId('codCarrera')
                  ->constrained('carreras', 'codCarrera')
                  ->onDelete('cascade');
            $table->unique(['idInscripcion', 'codCarrera']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrera__inscritos');
    }
};
