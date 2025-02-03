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
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('cascade');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->string('tipo');
            $table->integer('fardos');
            $table->decimal('pesoxfardo');
            $table->integer('stock');
            $table->string('unidad');
            $table->string('color');
            $table->integer('stock_min');
            $table->integer('stock_max');
            $table->decimal('costoLLegada',10,2);
            $table->decimal('costo_kilo',10,2);
            $table->decimal('costoTransporte',10,2)->default(0);
            $table->decimal('costoEnvio',10,2)->default(0);
            $table->decimal('impuetoInportacion',10,2)->default(0);
            $table->decimal('costoTotal',10,2)->default(0);
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
