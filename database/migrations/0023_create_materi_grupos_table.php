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
            $table->foreignId('idMateria')
                  ->constrained('materias', 'idMateria')
                  ->onDelete('cascade');
            $table->foreignId('codigoG')
                  ->constrained('grupos', 'codigoG')
                  ->onDelete('cascade');
            $table->primary(['idMateria', 'codigoG']);
            $table->timestamps();
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
