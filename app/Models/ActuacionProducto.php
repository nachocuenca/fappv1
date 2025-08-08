<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActuacionProducto extends Model
{
    use HasFactory;

    protected $table = 'actuacion_productos';

    protected $fillable = [
        'actuacion_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'iva_porcentaje',
        'irpf_porcentaje',
        'subtotal',
    ];

    public function actuacion()
    {
        return $this->belongsTo(Actuacion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

