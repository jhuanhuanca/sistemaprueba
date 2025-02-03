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
        Schema::create('orden_produccions', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('cantidad_producir');
            $table->string('color');
            $table->string('tipo_material');
            $table->string('usuario');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('fecha_finalizacion_estimada')->nullable();
            $table->timestamp('fecha_entrega')->nullable();
            $table->string('estado');
            $table->text('observaciones')->nullable();
            $table->decimal('costo_estimado', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_produccions');
    }
};
