<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Models\Factura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        // Facturas se crean desde actuaciones, aquí se deja vacío o con los campos necesarios
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->sortable(),

                Tables\Columns\TextColumn::make('serie')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('total')
                    ->money('eur')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                // ViewAction se quita si no hay vista individual de factura
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFacturas::route('/'),
            'create' => Pages\CreateFactura::route('/create'),
            'edit'   => Pages\EditFactura::route('/{record}/edit'),
        ];
    }
	
	public static function canCreate(): bool { return true; }
}
