<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Presupuesto;
use App\Models\PresupuestoProducto;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class FappSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'cliente']);

        // Users
        $admin = User::firstOrCreate(
            ['email' => 'nacho@nacho.es'],
            ['name' => 'nacho', 'password' => bcrypt('secret')]
        );
        $admin->assignRole('admin');

        // Cliente demo
        $c = Cliente::firstOrCreate([
            'usuario_id' => $admin->id,
            'nombre' => 'Cliente Demostración',
            'email' => 'cliente@demo.es',
        ]);

// después de crear/obtener $admin y $cliente

DB::table('presupuestos')
    ->where([
        'usuario_id' => $admin->id,
        'serie' => 'A',
        'numero' => 1,
    ])->delete();

        // Productos demo
        $p1 = Producto::firstOrCreate([
            'usuario_id' => $admin->id,
            'nombre' => 'Mano de obra',
        ], [
            'descripcion' => 'Hora de técnico',
            'precio' => 35,
        ]);

        $p2 = Producto::firstOrCreate([
            'usuario_id' => $admin->id,
            'nombre' => 'Split AC 3000fg',
        ], [
            'descripcion' => 'Aire acondicionado split 3.0kW',
            'precio' => 599,
        ]);


// Borrar presupuesto duplicado si existe
DB::table('presupuestos')
    ->where([
        'usuario_id' => 1,
        'serie' => 'A',
        'numero' => 1
    ])
    ->delete();

        // Presupuesto demo con líneas
        $manoObraSubtotal = 2 * 35;
        $splitSubtotal = 1 * 599;
        $base = $manoObraSubtotal + $splitSubtotal;
        $ivaTotal = round($base * 0.21, 2);
        $irpfTotal = 0;
        $total = $base + $ivaTotal - $irpfTotal;

        $pres = Presupuesto::create([
            'usuario_id' => $admin->id,
            'cliente_id' => $c->id,
            'fecha' => now()->toDateString(),
            'numero' => 1,
            'serie' => 'A',
            'estado' => 'borrador',
            'notas' => 'Presupuesto de ejemplo',
            'base_imponible' => $base,
            'iva_total' => $ivaTotal,
            'irpf_total' => $irpfTotal,
            'total' => $total,
        ]);

        PresupuestoProducto::create([
            'presupuesto_id' => $pres->id,
            'producto_id' => $p1->id,
            'descripcion' => 'Mano de obra',
            'cantidad' => 2,
            'precio_unitario' => 35,
            'subtotal' => $manoObraSubtotal,
        ]);

        PresupuestoProducto::create([
            'presupuesto_id' => $pres->id,
            'producto_id' => $p2->id,
            'descripcion' => 'Split AC 3000fg',
            'cantidad' => 1,
            'precio_unitario' => 599,
            'subtotal' => $splitSubtotal,
        ]);

        // Semillas adicionales
        $this->call(PresupuestoSeeder::class);
    }
}
