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
        Schema::create('reprocesados', function (Blueprint $table) {
            $table->id(); 
            $table->string('codigo', 100);
            $table->string('descripcion', 255); 
            $table->decimal('peso', 8, 2);
            $table->string('color', 100);
            $table->string('estado'); 
            $table->date('fecha'); 
            $table->time('hora_inicio');
            $table->time('hora_salida');
            $table->decimal('horas_trabajadas',8,2);
            $table->foreignId('equipo_id')->constrained('equipos')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('costoMaq', 8, 2);
            $table->foreignId('empleado_id')->constrained('empleados')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('costoEmp', 8, 2);
            $table->decimal('costoManoObra', 8, 2);
            $table->decimal('costokilo', 8, 2);
            $table->string('costoextra', 255)->nullable();
            $table->decimal('otroscostos', 8, 2)->default(0);
            $table->decimal('costoTotal', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reprocesados');
    }
};
