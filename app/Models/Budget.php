<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasTenant;

class Budget extends Model
{
    use HasFactory;
    use HasTenant;

    protected $table = 'budgets';

    protected $fillable = [
        'usuario_id',
        'nombre',
        'monto',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
