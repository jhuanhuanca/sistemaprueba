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
        Schema::create('produccion_diarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabricacion_id')->constrained('fabricacions')->onUpdate('cascade')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_salida');
            $table->integer('horas_trabajadas');
            $table->string('numero_lote');
            $table->string('producto');
            $table->integer('cantidad_producida');
            $table->string('color');
            $table->integer('desperdicios');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('restrict')->onUpdate('cascade');
            $table->string('usuario');
            $table->text('observaciones')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produccion_diarias');
    }
};
