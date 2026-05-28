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
        Schema::create('docente__grupos', function (Blueprint $table) {
            $table->foreignId('codigoDoc')
                  ->constrained('docentes', 'codigoDoc')
                  ->onDelete('cascade');
            $table->foreignId('codigoG')
                  ->constrained('grupos', 'codigoG')
                  ->onDelete('cascade');
            $table->primary(['codigoDoc', 'codigoG']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente__grupos');
    }
};
