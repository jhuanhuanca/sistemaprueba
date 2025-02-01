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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('marca');
            $table->string('modelo');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->string('estado')->default('operativo');
            $table->decimal('factorPotencia', 10, 2)->default(0);
            $table->decimal('voltaje', 10, 2)->default(0);
            $table->decimal('amperaje', 10, 2)->default(0);
            $table->decimal('consumoEnergetico', 10, 2)->default(0);
            $table->decimal('depreciacionAnual', 10, 2)->default(0);
            $table->decimal('costoMantenimiento', 10, 2)->default(0);
            $table->decimal('costoMaq', 10, 2)->default(0);
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
