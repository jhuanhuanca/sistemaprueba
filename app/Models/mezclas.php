<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mezclas extends Model
{
    use HasFactory;

    protected $table = 'mezclas';

    protected $fillable = [
        'codigo',
        'fecha',
        'usuario',
        'estado',
        'tipo',
        'master_id',
        'peso_master',
        'kilos_utilizados',
        'costo_master',
        'costo_mezcla',
        'costo_total',
        'virgen',
        'reciclado',
        'observaciones',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function masters()
    {
        return $this->belongsTo(masters::class, 'master_id');
    }

    public function mezclaMaterial()
    {
        return $this->hasMany(MezclaMaterial::class, 'mezcla_id');
    }

    public function fabricaciones(): HasMany
    {
        return $this->hasMany(Fabricacion::class, 'mezcla_id');
    }

    public function getKilosUtilizadosAttribute()
    {
        return $this->mezclaMaterial->sum('cantidad');
    }

    public function getCostoMezclaAttribute()
    {
        return $this->mezclaMaterial->sum('costo_total');
    }

    public function registrosMezcla()
    {
        return $this->hasMany(RegistrosMezcla::class, 'mezcla_id');
    }


    public function getRecicladoAttribute()
    {
        $totalKilos = $this->mezclaMaterial->sum('cantidad'); // Total de kilos en la mezcla
        if ($totalKilos > 0) {
            $kilosReciclado = $this->mezclaMaterial
                ->filter(function ($item) {
                    return $item->tipo === 'material reciclado';
                })
                ->sum('cantidad');
            
            return round(($kilosReciclado / $totalKilos) * 100, 2);
        }
        return 0;
    }

    public function getVirgenAttribute()
    {
        $totalKilos = $this->mezclaMaterial->sum('cantidad'); // Total de kilos en la mezcla
        if ($totalKilos > 0) {
            $kilosVirgen = $this->mezclaMaterial
                ->filter(function ($item) {
                    return $item->tipo === 'material virgen';
                })
                ->sum('cantidad');
            
            return round(($kilosVirgen / $totalKilos) * 100, 2);
        }
        return 0;
    }

    public function getCostoTotalAttribute()
    {
        $costoMezcla = $this->getCostoMezclaAttribute() ?? 0;
        $costoMaster = $this->costo_master ?? 0;
        
        return round($costoMezcla + $costoMaster, 2);
    }
    
}
