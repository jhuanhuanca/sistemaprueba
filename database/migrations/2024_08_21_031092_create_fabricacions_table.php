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
        Schema::create('fabricacions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->foreignId('orden_produccion_id')->constrained('orden_produccions')->onDelete('cascade');
            $table->string('area');
            $table->string('producto');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('costo_maq', 10, 2);
            $table->foreignId('mezcla_id')->constrained('mezclas')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string('tipo_material');
            $table->integer('cantidad_a_producir');
            $table->integer('cantidad_mezclas');
            $table->decimal('costo_mezcla', 10, 2);
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade')->onUpdate('cascade');
            $table->string('usuario');
            $table->date('fecha_inicio');
            $table->date('fecha_finalizacion');
            $table->string('estado');
            $table->decimal('costo_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabricacions');
    }
};
