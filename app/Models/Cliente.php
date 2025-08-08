<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'usuario_id', 'nombre', 'cif', 'email', 'telefono', 'direccion'
    ];

    public function scopeMine($query)
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $query->where('usuario_id', $user->id);
        }
        return $query;
    }
}
