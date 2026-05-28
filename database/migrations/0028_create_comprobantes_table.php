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
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id('idComprobante');
            $table->string('codigo', 50)->unique();
            $table->string('nroComprobante', 50);
            $table->string('concepto', 255)->nullable();
            $table->date('fecha');
            $table->foreignId('nroPago')
                  ->constrained('pagos', 'nroPago')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
