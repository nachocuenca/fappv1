<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresupuestoProducto extends Model
{
    use HasFactory;

    protected $table = 'presupuesto_productos';

    protected $fillable = [
        'presupuesto_id','producto_id','descripcion','cantidad','precio_unitario',
        'iva_porcentaje','irpf_porcentaje','subtotal'
    ];

    public function presupuesto() { return $this->belongsTo(Presupuesto::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
