<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SeriesService
{
    public static function nextNumber(int $usuarioId, string $tipo, string $serie = 'A'): int
    {
        return DB::transaction(function () use ($usuarioId, $tipo, $serie) {
            $row = DB::table('series')->lockForUpdate()
                ->where('usuario_id', $usuarioId)
                ->where('tipo', $tipo)
                ->where('serie', $serie)
                ->first();

            if (!$row) {
                DB::table('series')->insert([
                    'usuario_id' => $usuarioId,
                    'tipo' => $tipo,
                    'serie' => $serie,
                    'siguiente_numero' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return 1;
            }

            $num = (int) $row->siguiente_numero;
            DB::table('series')->where('id', $row->id)->update([
                'siguiente_numero' => $num + 1,
                'updated_at' => now(),
            ]);
            return $num;
        });
    }
}
