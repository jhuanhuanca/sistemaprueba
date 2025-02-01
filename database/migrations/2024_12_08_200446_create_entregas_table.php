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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->date('fecha');
            $table->string('usuario');
            $table->foreignId('orden_produccion_id')->constrained('orden_produccions')->onDelete('cascade');
            $table->string('producto');
            $table->string('color');
            $table->integer('total');
            $table->integer('entregado');
            $table->integer('faltante');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
