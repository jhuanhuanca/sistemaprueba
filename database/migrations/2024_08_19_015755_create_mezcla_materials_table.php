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
        Schema::create('mezcla_materials', function (Blueprint $table) {
            $table->id();
        $table->foreignId('mezcla_id')->constrained('mezclas')->onDelete('cascade')->onUpdate('cascade');
        $table->string('codigo'); 
        $table->string('tipo_material');   
        $table->foreignId('insumo_id')->nullable()->constrained('insumos')->onDelete('restrict')->onUpdate('cascade');
        $table->foreignId('reprocesados_id')->nullable()->constrained('reprocesados')->onDelete('restrict')->onUpdate('cascade');
        $table->string('tipo');
        $table->decimal('cantidad', 10, 2);
        $table->decimal('costo_kilo', 10, 2);
        $table->decimal('costo_total', 10, 2);
        $table->string('color');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mezcla_materials');
    }
};
