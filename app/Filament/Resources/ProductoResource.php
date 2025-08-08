<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('descripcion')
                ->maxLength(500),

            Forms\Components\TextInput::make('precio')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('iva_porcentaje')
                ->numeric()
                ->default(21),

            Forms\Components\Toggle::make('activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50),

                Tables\Columns\TextColumn::make('precio')
                    ->money('eur')
                    ->sortable(),

                Tables\Columns\TextColumn::make('iva_porcentaje')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
            ])
            ->filters([])
            ->actions([
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
            'index'  => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit'   => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
	
	public static function canCreate(): bool { return true; }
}
