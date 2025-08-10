<?php

namespace App\Filament\Resources\PresupuestoResource\Pages;

use App\Filament\Resources\PresupuestoResource;
use Filament\Resources\Pages\EditRecord;
use App\Services\CalculaTotales;

class EditPresupuesto extends EditRecord
{
    protected static string $resource = PresupuestoResource::class;

    protected function afterSave(): void
    {
        $p = $this->record->fresh(['lineas']);
        $t = CalculaTotales::deLineas($p->lineas ?? []);
        $p->fill($t)->saveQuietly();
    }
}
