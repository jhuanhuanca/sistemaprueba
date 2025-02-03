<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetallePedido extends Model
{
    use HasFactory;

    protected $table = 'detalles_pedido';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'subproducto_id',
        'cantidad',
        'precio_unitario',
        'total'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Antes de guardar, calcular el total del detalle
        static::saving(function ($detalle) {
            if ($detalle->cantidad && $detalle->precio_unitario) {
                $detalle->total = $detalle->cantidad * $detalle->precio_unitario;
            }
        });

        // Después de cualquier cambio, actualizar el total del pedido
        static::saved(function ($detalle) {
            if ($detalle->pedido) {
                $detalle->pedido->refresh();
                $nuevoTotal = $detalle->pedido->detalles_pedido()->sum('total');
                $detalle->pedido->update(['total' => $nuevoTotal]);
            }
        });

        static::deleted(function ($detalle) {
            if ($detalle->pedido) {
                $detalle->pedido->refresh();
                $nuevoTotal = $detalle->pedido->detalles_pedido()->sum('total');
                $detalle->pedido->update(['total' => $nuevoTotal]);
            }
        });
    }

    // Relación con Pedido
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación con Producto
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    // Relación con SubProducto
    public function subproducto(): BelongsTo
    {
        return $this->belongsTo(SubProducto::class, 'subproducto_id');
    }
} 