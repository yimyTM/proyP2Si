<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            // Agrupa los permisos para mostrarlos por secciones en la UI
            $table->string('categoria', 100)->default('General')->after('nombrePermiso');
        });
    }

    public function down(): void
    {
        Schema::table('permisos', function (Blueprint $table) {
            $table->dropColumn('categoria');
        });
    }
};
