<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;


    protected $fillable = [
        'usuario_id','cliente_id','presupuesto_id','actuacion_id','fecha','numero','serie','estado','notas',
        'base_imponible','iva_total','irpf_total','total'
    ];

    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function actuacion() { return $this->belongsTo(Actuacion::class); }
    public function presupuesto() { return $this->belongsTo(Presupuesto::class); }
    public function lineas() { return $this->hasMany(PedidoProducto::class); }

}
