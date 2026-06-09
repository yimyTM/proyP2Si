<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->unsignedBigInteger('codCarreraAsignada')->nullable()->after('resultado');
            $table->string('estado_admision', 20)->nullable()->after('codCarreraAsignada');

            $table->foreign('codCarreraAsignada')
                  ->references('codCarrera')
                  ->on('carreras')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropForeign(['codCarreraAsignada']);
            $table->dropColumn(['codCarreraAsignada', 'estado_admision']);
        });
    }
};
