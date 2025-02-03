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
        Schema::create('almacen_reprocesados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 100);
            $table->string('descripcion', 255);
            $table->decimal('peso', 8, 2);
            $table->string('color', 100);
            $table->string('estado');
            $table->date('fecha');
            $table->decimal('costokilo', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen_reprocesados');
    }
};
