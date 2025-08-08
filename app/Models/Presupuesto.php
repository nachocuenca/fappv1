<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presupuesto extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id','cliente_id','fecha','numero','serie','estado','validez_dias','notas','activo',
        'base_imponible','iva_total','irpf_total','total'
    ];

    protected $casts = [
        'fecha' => 'date',
        'validez_dias' => 'integer',
        'activo' => 'boolean',
        'base_imponible' => 'decimal:2',
        'iva_total' => 'decimal:2',
        'irpf_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopeMine($query)
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $query->where('usuario_id', $user->id);
        }
        return $query;
    }
}
