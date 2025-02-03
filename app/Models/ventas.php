<?php

namespace App\Models;

use App\Filament\Resources\AlmacenResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ventas extends Model
{
    use HasFactory;
    protected $fillable = [
        'codigo',
        'almacen_id',
        'cliente_id',
        'fecha_venta',
        'usuario',
        'total',
        'metodo_pago',
        'estado',
    ];

    protected $attributes = [
        'almacen_id' => 1,
    ];

    public function DetalleVentas()
    {
        return $this->hasMany(DetalleVentas::class,'venta_id');
    }
    public function clientes()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }
    public function almacenes(): BelongsTo
    {
        return $this->belongsTo(Almacenes::class, 'almacen_id');
    }
    
    public function getTotalAttribute()
{
    return $this->detalleVentas->sum('subtotal');
}
}
