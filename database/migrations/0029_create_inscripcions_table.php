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
        Schema::create('inscripcions', function (Blueprint $table) {
            $table->id('idInscripcion');
            $table->date('fecha');
            $table->string('estado', 20)->default('activa');
            $table->foreignId('idPost')
                  ->constrained('postulantes', 'idPost')
                  ->onDelete('cascade');
            $table->foreignId('idGestion')
                  ->constrained('gestiones', 'idGestion')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcions');
    }
};
