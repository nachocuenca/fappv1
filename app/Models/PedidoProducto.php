<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoProducto extends Model
{
    use HasFactory;

    protected $table = 'pedido_productos';

    protected $fillable = [
        'pedido_id','producto_id','descripcion','cantidad','precio_unitario',
        'iva_porcentaje','irpf_porcentaje','subtotal'
    ];

    public function pedido() { return $this->belongsTo(Pedido::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
