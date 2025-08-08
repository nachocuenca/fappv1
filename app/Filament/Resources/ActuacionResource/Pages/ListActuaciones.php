<?php

namespace App\Filament\Resources\ActuacionResource\Pages;

use App\Filament\Resources\ActuacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ListActuaciones extends ListRecords
{
    protected static string $resource = ActuacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nueva Actuación'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            ExportBulkAction::make('export')
                ->label('Exportar Seleccionados')
                ->exports([
                    \pxlrbt\FilamentExcel\Exports\ExcelExport::make(),
                ]),
        ];
    }
}
