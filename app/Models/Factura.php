<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id','cliente_id','presupuesto_id',
        'fecha','numero','serie','estado','notas',
        'base_imponible','iva_total','irpf_total','total'
    ];

    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function presupuesto() { return $this->belongsTo(Presupuesto::class); }
    public function lineas() { return $this->hasMany(FacturaProducto::class); }
    public function actuaciones() { return $this->belongsToMany(Actuacion::class, 'actuacion_factura'); }

}
