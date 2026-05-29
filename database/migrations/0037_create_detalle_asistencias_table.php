<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_asistencias', function (Blueprint $table) {
            $table->id('idDetalle');
            $table->foreignId('idAsistencia')
                  ->constrained('asistencias', 'idAsistencia')
                  ->onDelete('cascade');
            $table->foreignId('idPost')
                  ->constrained('postulantes', 'idPost')
                  ->onDelete('cascade');
            $table->enum('estado', ['presente', 'ausente', 'tardanza'])->default('ausente');
            $table->timestamps();
            $table->unique(['idAsistencia', 'idPost']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_asistencias');
    }
};
