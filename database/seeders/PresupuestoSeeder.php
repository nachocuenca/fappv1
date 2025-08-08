<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presupuesto;
use App\Models\Cliente;

class PresupuestoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::all();

        if ($clientes->count() === 0) {
            $this->command->warn('No hay clientes, se necesitan clientes para generar presupuestos.');
            return;
        }

        foreach (range(1, 15) as $i) {
            $cliente = $clientes->random();
            $base = rand(100, 2000);
            $ivaTotal = round($base * 0.21, 2);
            $irpfTotal = 0;
            $total = $base + $ivaTotal - $irpfTotal;

            Presupuesto::create([
                'usuario_id' => $cliente->usuario_id,
                'serie' => 'PRES',
                'numero' => $i,
                'fecha' => now()->subDays(rand(0, 90)),
                'cliente_id' => $cliente->id,
                'base_imponible' => $base,
                'iva_total' => $ivaTotal,
                'irpf_total' => $irpfTotal,
                'total' => $total,
                'estado' => collect(['borrador', 'enviado', 'aceptado', 'rechazado'])->random(),
                'notas' => 'Notas del presupuesto ' . $i,
            ]);
        }
    }
}
