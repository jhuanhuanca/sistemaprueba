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
        Schema::create('produccion_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fabricacion_id')->constrained('fabricacions')->onDelete('cascade');
            $table->foreignId('produccion_diaria_id')->constrained('produccion_diarias')->onDelete('cascade');
            $table->integer('cantidad');
            $table->string('producto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produccion_finals');
    }
};
