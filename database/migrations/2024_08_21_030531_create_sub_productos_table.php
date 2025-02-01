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
        Schema::create('sub_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre');
            $table->string('color');
            $table->string('tipo');
            $table->integer('cantidad');
            $table->integer('disponible');
            $table->integer('unidxpaq');
            $table->integer('paq');
            $table->decimal('peso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_productos');
    }
};
