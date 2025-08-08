<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Cliente;
use Spatie\Permission\Models\Role;

class PresupuestoResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/fapp']);
    }

    public function test_admin_sees_all_clients_in_select(): void
    {
        Role::create(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $other = User::factory()->create();

        Cliente::create([
            'usuario_id' => $admin->id,
            'nombre' => 'Admin Client',
            'cif' => 'A1',
            'email' => 'admin@example.com',
            'telefono' => '111111111',
            'direccion' => 'Street 1',
        ]);

        Cliente::create([
            'usuario_id' => $other->id,
            'nombre' => 'Other Client',
            'cif' => 'B2',
            'email' => 'other@example.com',
            'telefono' => '222222222',
            'direccion' => 'Street 2',
        ]);

        $this->actingAs($admin);

        $options = Cliente::query()
            ->when(
                auth()->check() && !auth()->user()->hasRole('admin'),
                fn ($q) => $q->where('usuario_id', auth()->id())
            )
            ->pluck('nombre', 'id');

        $this->assertCount(2, $options);
    }

    public function test_non_admin_sees_only_their_clients_in_select(): void
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create();
        $other = User::factory()->create();

        Cliente::create([
            'usuario_id' => $user->id,
            'nombre' => 'My Client',
            'cif' => 'C1',
            'email' => 'my@example.com',
            'telefono' => '333333333',
            'direccion' => 'Street 3',
        ]);

        Cliente::create([
            'usuario_id' => $other->id,
            'nombre' => 'Other Client',
            'cif' => 'D4',
            'email' => 'other2@example.com',
            'telefono' => '444444444',
            'direccion' => 'Street 4',
        ]);

        $this->actingAs($user);

        $options = Cliente::query()
            ->when(
                auth()->check() && !auth()->user()->hasRole('admin'),
                fn ($q) => $q->where('usuario_id', auth()->id())
            )
            ->pluck('nombre', 'id');

        $this->assertCount(1, $options);
        $this->assertEquals(['My Client'], $options->values()->toArray());
    }
}
