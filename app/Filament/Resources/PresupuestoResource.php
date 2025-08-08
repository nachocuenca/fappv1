<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresupuestoResource\Pages;
use App\Models\Presupuesto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class PresupuestoResource extends Resource
{
    protected static ?string $model = Presupuesto::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Presupuestos';
    protected static ?string $pluralLabel = 'Presupuestos';
    protected static ?string $modelLabel = 'Presupuesto';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('serie')->required(),
            Forms\Components\TextInput::make('numero')->numeric()->required(),
            Forms\Components\DatePicker::make('fecha')->required(),
            Forms\Components\TextInput::make('validez_dias')->numeric()->nullable(),
            Forms\Components\Select::make('cliente_id')
                ->relationship('cliente', 'nombre')
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('base_imponible')->numeric()->required(),
            Forms\Components\TextInput::make('iva_total')->numeric()->default(0),
            Forms\Components\TextInput::make('irpf_total')->numeric()->default(0),
            Forms\Components\TextInput::make('total')->numeric()->required(),
            Forms\Components\Select::make('estado')
                ->options([
                    'borrador' => 'Borrador',
                    'enviado' => 'Enviado',
                    'aceptado' => 'Aceptado',
                    'rechazado' => 'Rechazado',
                ])
                ->default('borrador')
                ->rules(['required', Rule::in(['borrador', 'enviado', 'aceptado', 'rechazado'])]),
            Forms\Components\Toggle::make('activo')->default(true),
            Forms\Components\Textarea::make('notas'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serie')->sortable(),
                Tables\Columns\TextColumn::make('numero')->sortable(),
                Tables\Columns\TextColumn::make('fecha')->date()->sortable(),
                Tables\Columns\TextColumn::make('validez_dias')->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('base_imponible')->money('EUR'),
                Tables\Columns\TextColumn::make('iva_total')->money('EUR'),
                Tables\Columns\TextColumn::make('irpf_total')->money('EUR'),
                Tables\Columns\TextColumn::make('total')->money('EUR'),
                Tables\Columns\TextColumn::make('estado')->badge(),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return auth()->user()?->hasRole('admin') ? $query : $query->mine();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresupuestos::route('/'),
            'create' => Pages\CreatePresupuesto::route('/create'),
            'edit' => Pages\EditPresupuesto::route('/{record}/edit'),
        ];
    }
}
