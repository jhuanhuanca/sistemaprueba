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
        Schema::create('masters', function (Blueprint $table) {
            $table->id(); 
            $table->string('codigo'); 
            $table->string('descripcion');
            $table->string('color', 100);
            $table->decimal('peso', 8, 2);
            $table->decimal('precioLLegada',8,2);
            $table->decimal('precioPorGramo', 8, 2);
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('costoTransporte', 8, 2)->default(0);
            $table->decimal('costoEnvio', 8, 2)->default(0);
            $table->decimal('costofinal', 8, 2)->default(0);
            $table->date('fecha'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masters');
    }
};
