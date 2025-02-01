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
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->foreignId('fabricacion_id')->constrained('fabricacions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('asiento_id')->constrained('asientos')->onDelete('cascade')->onUpdate('cascade');
            $table->string('descripcion');
            $table->decimal('manoobra', 10, 2)->default(0); 
            $table->decimal('costoextra', 10, 2)->default(0);   
            $table->decimal('costoproceso', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
