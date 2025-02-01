<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asientos extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'descripcion',
    ];
    
    protected $table = 'asientos';
    
    public function procesos()
    {
        return $this->hasMany(Procesos::class, 'asiento_id');
    }
}
