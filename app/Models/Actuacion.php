<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actuacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id','cliente_id','codigo','fecha_inicio','fecha_fin','estado','notas'
    ];

    public function user() { return $this->belongsTo(User::class, 'usuario_id'); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function pedidos() { return $this->hasMany(Pedido::class); }
    public function productos() { return $this->hasMany(ActuacionProducto::class); }
    public function facturas() { return $this->belongsToMany(Factura::class, 'actuacion_factura'); }

    public function scopeMine($query)
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $query->where('usuario_id', $user->id);
        }
        return $query;
    }
}
