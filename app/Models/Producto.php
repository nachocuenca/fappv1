<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
	
	protected $casts = [
    'precio'         => 'decimal:2',
    'iva_porcentaje' => 'decimal:2',
    'activo'         => 'boolean',
];

    protected $fillable = [
        'usuario_id', 'nombre', 'descripcion', 'precio', 'iva_porcentaje', 'activo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
