<?php

namespace App\Filament\Resources\ActuacionResource\Pages;

use App\Filament\Resources\ActuacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActuacion extends EditRecord
{
    protected static string $resource = ActuacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
