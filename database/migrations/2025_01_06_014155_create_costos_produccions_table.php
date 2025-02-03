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
        Schema::create('costos_produccions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produccion_total_id')->constrained('produccion_totales')->onDelete('cascade');
            $table->string('codigo_fabricacion');
            $table->string('producto');
            $table->decimal('cantidad', 10, 2); 
            $table->decimal('costoProduccion', 10, 2)->default(0);
            $table->decimal('costoUnidad', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costos_produccions');
    }
};
