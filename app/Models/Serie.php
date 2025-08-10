<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Serie extends Model
{
    protected $table = 'series';
    protected $fillable = ['usuario_id','tipo','serie','siguiente_numero'];

    public static function nextNumberFor(int $usuarioId, string $tipo, string $serie): int
    {
        return DB::transaction(function () use ($usuarioId, $tipo, $serie) {
            $row = static::where([
                'usuario_id' => $usuarioId,
                'tipo'       => $tipo,
                'serie'      => $serie,
            ])->lockForUpdate()->first();

            if (! $row) {
                $row = static::create([
                    'usuario_id' => $usuarioId,
                    'tipo'       => $tipo,
                    'serie'      => $serie,
                    'siguiente_numero' => 1,
                ]);
            }

            $num = (int) $row->siguiente_numero;
            $row->increment('siguiente_numero');

            return $num;
        }, 3);
    }
}
