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
        Schema::create('registros_mezcla', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->foreignId('fabricacion_id')->constrained('fabricacions')->onDelete('cascade');
            $table->foreignId('mezcla_id')->constrained('mezclas')->onDelete('cascade') ;
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->integer('cantidad_por_mezclar');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('horas_trabajadas');
            $table->decimal('mano_obra', 10, 2);
            $table->decimal('costo_maquina', 10, 2);
            $table->decimal('costo_total', 10, 2);
            $table->integer('cantidad_mezcladas');
            $table->string('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_mezcla');
    }
};
