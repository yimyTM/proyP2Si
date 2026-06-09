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
        Schema::create('users', function (Blueprint $table) {
            $table->id('idUsuario');
            $table->string('nombreCompleto', 150);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 15)->nullable();
            $table->string('correo', 150)->unique();
            $table->string('password', 255);
            $table->rememberToken();
            $table->foreignId('idRol')->constrained('rols', 'idRol')->onDelete('cascade');
            $table->boolean('estado')->default(true);  
            $table->timestamp('ultimo_acceso')->nullable();        
            $table->integer('intentos_fallidos')->default(0);      
            $table->timestamp('bloqueado_hasta')->nullable();  
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }

};
