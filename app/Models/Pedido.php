<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'almacen_id',
        'cliente',
        'telefono',
        'fecha',
        'total'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relación con Almacén
    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacenes::class);
    }

    // Relación con Cliente
    public function clienteRelacion(): BelongsTo
    {
        return $this->belongsTo(Clientes::class, 'cliente');
    }

    public function detalles_pedido()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function actualizarTotal()
    {
        $this->total = $this->detalles_pedido->sum('total');
        $this->save();
    }
} 