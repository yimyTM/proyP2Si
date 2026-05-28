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
        Schema::create('postulantes', function (Blueprint $table) {
            $table->id('idPost');
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('ci', 20)->unique();
            $table->string('nroTelefono', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->char('sexo', 1)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('colegio_procedencia', 150)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('contrasena', 255)->nullable();
            $table->foreignId('idUsuario')
                  ->nullable()
                  ->constrained('usuarios', 'idUsuario')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulantes');
    }
};
