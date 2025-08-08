<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaProducto extends Model
{
    use HasFactory;

    protected $table = 'factura_productos';

    protected $fillable = [
        'factura_id','producto_id','descripcion','cantidad','precio_unitario',
        'iva_porcentaje','irpf_porcentaje','subtotal'
    ];

    public function factura() { return $this->belongsTo(Factura::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
