<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presupuesto extends Model
{
    use HasFactory;

    protected $fillable = [
        'serie',
        'numero',
        'fecha',
        'cliente_id',
        'base_imponible',
        'iva_porcentaje',
        'total',
        'estado',
        'observaciones',
        'activo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
