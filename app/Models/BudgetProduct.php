<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetProduct extends Model
{
    protected $table = 'budget_products';

    protected $fillable = [
        'budget_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'iva_porcentaje',
        'irpf_porcentaje',
        'subtotal',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
