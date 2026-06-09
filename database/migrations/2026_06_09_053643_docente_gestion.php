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
        Schema::create('docente_gestion', function (Blueprint $table) {
            $table->foreignId('codigoDoc')
                  ->constrained('docentes', 'codigoDoc')
                  ->onDelete('cascade');
            $table->foreignId('idGestion')
                  ->constrained('gestions', 'idGestion')
                  ->onDelete('cascade');
            $table->date('fecha_contrato');
            $table->string('estado', 30)->default('No Contratado');
            $table->primary(['codigoDoc', 'idGestion']);
            $table->timestamps(); 
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_gestion');
    }
};
