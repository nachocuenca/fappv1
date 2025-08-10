<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // No aplicar en consola ni sin usuario
        if (app()->runningInConsole()) return;
        $user = auth()->user();
        if (!$user || $user->hasRole('admin')) return;

        // Solo si la tabla tiene 'usuario_id'
        $table = $model->getTable();
        if (! Schema::hasColumn($table, 'usuario_id')) return;

        // NO uses qualifyColumn ni getModel() del builder
        $builder->where("$table.usuario_id", $user->id);
    }
}
