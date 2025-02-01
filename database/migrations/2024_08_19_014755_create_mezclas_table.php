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
        Schema::create('mezclas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->timestamp('fecha')->useCurrent();
            $table->string('usuario');
            $table->string('estado');
            $table->string('tipo');
            $table->integer('kilos_utilizados');
            $table->foreignId('master_id')->constrained('masters')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('peso_master', 10, 2)->default(0);
            $table->decimal('costo_master', 10, 2)->default(0);
            $table->decimal('costo_mezcla', 10, 2)->default(0);
            $table->decimal('costo_total', 10, 2)-> default(0);
            $table->decimal('virgen', 10, 2)->default(0);
            $table->decimal('reciclado', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mezclas');
    }
    
};
