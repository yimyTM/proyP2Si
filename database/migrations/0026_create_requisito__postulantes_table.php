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
        Schema::create('requisito__postulantes', function (Blueprint $table) {
            $table->id('idReqPos');
            $table->date('fecha_entrega')->nullable();
            $table->boolean('entregado')->default(false);
            $table->boolean('validado')->default(false);
            $table->foreignId('idReq')
                  ->constrained('requisitos', 'idReq')
                  ->onDelete('cascade');
            $table->foreignId('idPost')
                  ->constrained('postulantes', 'idPost')
                  ->onDelete('cascade');
            $table->unique(['idReq', 'idPost']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisito__postulantes');
    }
};
