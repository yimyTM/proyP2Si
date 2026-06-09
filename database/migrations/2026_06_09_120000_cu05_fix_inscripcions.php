<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make codigoG nullable so inscripciones can be created before group assignment
        DB::statement('ALTER TABLE inscripcions ALTER COLUMN "codigoG" DROP NOT NULL');

        Schema::table('inscripcions', function (Blueprint $table) {
            $table->text('motivo_rechazo')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropColumn('motivo_rechazo');
        });

        DB::statement('ALTER TABLE inscripcions ALTER COLUMN "codigoG" SET NOT NULL');
    }
};
