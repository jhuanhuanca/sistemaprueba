<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produccion_totales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabricacion_id')->constrained('fabricacions');
            $table->string('producto');
            $table->decimal('costoproceso', 10, 2)->default(0);
            $table->decimal('costoMaq', 10, 2)->default(0);
            $table->decimal('costoManoObra', 10, 2)->default(0);
            $table->decimal('costoTotal', 10, 2)->default(0);
            $table->decimal('total_cantidad', 10, 2);
            $table->decimal('cantidad_entregada', 10, 2)->default(0);
            $table->decimal('cantidad_disponible', 10, 2);
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('produccion_totales');
    }
}; 