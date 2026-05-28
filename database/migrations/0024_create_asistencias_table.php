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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id('idAsistencia');
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->foreignId('codigoG')
                  ->constrained('grupos', 'codigoG')
                  ->onDelete('cascade');
            $table->foreignId('codigoDoc')
                  ->constrained('docentes', 'codigoDoc')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
