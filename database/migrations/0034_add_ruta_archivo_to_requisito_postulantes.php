<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requisito__postulantes', function (Blueprint $table) {
            // Ruta del archivo adjunto (ej. Título de Bachiller en PDF/imagen)
            $table->string('ruta_archivo', 500)->nullable()->after('validado');
        });
    }

    public function down(): void
    {
        Schema::table('requisito__postulantes', function (Blueprint $table) {
            $table->dropColumn('ruta_archivo');
        });
    }
};
