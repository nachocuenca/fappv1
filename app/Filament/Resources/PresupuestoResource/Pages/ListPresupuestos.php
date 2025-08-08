<?php

namespace App\Filament\Resources\PresupuestoResource\Pages;

use App\Filament\Resources\PresupuestoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;

class ListPresupuestos extends ListRecords
{
    protected static string $resource = PresupuestoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo Presupuesto'),
            ExportAction::make('export')->label('Exportar'),
        ];
    }
}
