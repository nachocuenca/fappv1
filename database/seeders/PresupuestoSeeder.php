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
            $iva = 21;
            $total = $base + ($base * $iva / 100);

            Presupuesto::create([
                'serie' => 'PRES',
                'numero' => $i,
                'fecha' => now()->subDays(rand(0, 90)),
                'cliente_id' => $cliente->id,
                'base_imponible' => $base,
                'iva_porcentaje' => $iva,
                'total' => $total,
                'estado' => collect(['pendiente', 'aceptado', 'rechazado'])->random(),
                'observaciones' => 'Observaciones del presupuesto ' . $i,
                'activo' => 1,
            ]);
        }
    }
}
