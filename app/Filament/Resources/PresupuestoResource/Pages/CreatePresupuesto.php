<?php

namespace App\Filament\Resources\PresupuestoResource\Pages;

use App\Filament\Resources\PresupuestoResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\CalculaTotales;

class CreatePresupuesto extends CreateRecord
{
    protected static string $resource = PresupuestoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user  = auth()->user();
        $serie = $user?->serie_presupuesto ?? 'A';

        $data['usuario_id'] = $user->id;
        $data['serie']      = $serie;
        $data['numero']     = \App\Models\Serie::nextNumberFor($user->id, 'presupuesto', $serie);
        $data['fecha']      = $data['fecha'] ?? now()->toDateString();

        return $data;
    }

    protected function afterCreate(): void
    {
        $p = $this->record->fresh(['lineas']);
        $t = CalculaTotales::deLineas($p->lineas ?? []);
        $p->fill($t)->saveQuietly();
    }
}
