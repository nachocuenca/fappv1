<?php

namespace App\Filament\Resources\ActuacionResource\RelationManagers;

use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductosRelationManager extends RelationManager
{
    protected static string $relationship = 'productos';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('producto_id')
                ->relationship('producto', 'nombre')
                ->searchable()
                ->label('Producto'),
            Forms\Components\TextInput::make('descripcion')
                ->required(),
            Forms\Components\TextInput::make('cantidad')
                ->numeric()
                ->default(1)
                ->required(),
            Forms\Components\TextInput::make('precio_unitario')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\TextInput::make('iva_porcentaje')
                ->numeric()
                ->default(21)
                ->required(),
            Forms\Components\TextInput::make('irpf_porcentaje')
                ->numeric()
                ->nullable(),
            Forms\Components\TextInput::make('subtotal')
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('producto.nombre')
                ->label('Producto')
                ->searchable(),
            Tables\Columns\TextColumn::make('descripcion')
                ->searchable(),
            Tables\Columns\TextColumn::make('cantidad'),
            Tables\Columns\TextColumn::make('precio_unitario')
                ->money('eur'),
            Tables\Columns\TextColumn::make('iva_porcentaje'),
            Tables\Columns\TextColumn::make('irpf_porcentaje'),
            Tables\Columns\TextColumn::make('subtotal')
                ->money('eur'),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}

