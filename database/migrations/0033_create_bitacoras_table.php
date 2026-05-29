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
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id('idBitacora');
            $table->text('descripcion');
            $table->date('fecha');
            $table->time('hora');
            $table->string('direccionIP', 45);
            $table->foreignId('idUsuario')
                  ->nullable()
                  ->constrained('users', 'idUsuario')
                  ->onDelete('set null');
            $table->timestamps();

            $table->index('fecha');
            $table->index('idUsuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
