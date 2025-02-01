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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rut_cif_nit')->unique();
            $table->string('ciudad');
            $table->string('codigo_postal');
            $table->string('pais');
            $table->string('telefono');
            $table->string('email')->unique();
            $table->string('sitio_web')->nullable();
            $table->date('fecha_registro');
            $table->string('estado')->default('activo');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
