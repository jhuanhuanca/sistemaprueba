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
        Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->string('codigo');
        $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('cascade');
        $table->foreignId('cliente_id')->constrained('clientes')->onUpdate('cascade')->onDelete('cascade');
        $table->timestamp('fecha_venta');
        $table->string('usuario');
        $table->decimal('total', 8, 2);
        $table->string('metodo_pago');
        $table->string('estado');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
