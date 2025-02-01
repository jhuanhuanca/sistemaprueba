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
        Schema::create('control_produccions', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('produccion_diaria_id')->constrained('produccion_diarias')->onDelete('restrict')->onUpdate('cascade');
            $table->string('nombre_operario');
            $table->string('usuario');
            $table->integer('produccion_aceptada');
            $table->integer('produccion_rechazada');
            $table->string('resultado'); // Ejemplo: 'aprobado', 'rechazado'
            $table->text('defectos_encontrados')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_produccions');
    }
};
