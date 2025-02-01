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
        Schema::create('control_calidads', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict')->onUpdate('cascade');
            $table->string('usuario');
            $table->string('punto_inspeccion');
            $table->string('resultado'); // Ejemplo: 'aprobado', 'rechazado'
            $table->text('defectos_encontrados')->nullable();
            $table->json('mediciones')->nullable(); // Puedes almacenar diferentes mediciones en formato JSON
            $table->text('accion_correctiva')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_calidads');
    }
};
