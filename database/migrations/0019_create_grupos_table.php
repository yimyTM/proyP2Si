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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id('codigoG');
            $table->integer('capacidad');
            $table->foreignId('codeModalidad')
                  ->constrained('modalidades', 'codeModalidad')
                  ->onDelete('cascade');
            $table->foreignId('idTurno')
                  ->constrained('turnos', 'idTurno')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
