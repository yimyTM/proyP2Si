<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->decimal('promedio', 5, 2)->nullable()->after('motivo_rechazo');
            $table->string('resultado', 20)->nullable()->after('promedio');
        });
    }

    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropColumn(['promedio', 'resultado']);
        });
    }
};
