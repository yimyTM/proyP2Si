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
        Schema::create('gestion_carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idGestion')->constrained('gestions', 'idGestion')->onDelete('cascade');
            $table->foreignId('codCarrera')->constrained('carreras', 'codCarrera')->onDelete('cascade');
            $table->integer('cupos')->unsigned();
            $table->unique(['idGestion', 'codCarrera']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestion_carreras');
    }
};
