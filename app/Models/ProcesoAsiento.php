<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoAsiento extends Model
{
    protected $table = 'proceso_asiento';
    
    protected $fillable = [
        'codigo',
        'proceso_id',
        'asiento_id'
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Procesos::class);
    }

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(asientos::class);
    }
} 