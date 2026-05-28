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
        Schema::create('grupo__horarios', function (Blueprint $table) {
            $table->foreignId('codigoG')
                  ->constrained('grupos', 'codigoG')
                  ->onDelete('cascade');
            $table->foreignId('idHorario')
                  ->constrained('horarios', 'idHorario')
                  ->onDelete('cascade');
            $table->primary(['codigoG', 'idHorario']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo__horarios');
    }
};
