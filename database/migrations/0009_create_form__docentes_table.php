<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
    {
        Schema::create('form_docente', function (Blueprint $table) {
            $table->foreignId('codigoDoc')
                  ->constrained('docentes', 'codigoDoc')
                  ->onDelete('cascade');
            $table->foreignId('idForm')
                  ->constrained('form_academicas', 'idForm')
                  ->onDelete('cascade');
            $table->primary(['codigoDoc', 'idForm']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_docente');
    }
};
