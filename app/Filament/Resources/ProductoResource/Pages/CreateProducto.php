<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['usuario_id'] = auth()->id();
        return $data;
    }
}
