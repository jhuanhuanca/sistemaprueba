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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->decimal('peso_unitario',10,2);
            $table->string('image');
            $table->integer('stock_min');
            $table->integer('stock_max');
            $table->decimal('costo_mercado',10,2);
            $table->string('estado');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
