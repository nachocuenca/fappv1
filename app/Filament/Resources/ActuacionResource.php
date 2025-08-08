<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActuacionResource\Pages;
use App\Models\Actuacion;
use App\Models\Cliente;
use App\Filament\Resources\ActuacionResource\RelationManagers\ProductosRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ActuacionResource extends Resource
{
    protected static ?string $model = Actuacion::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
	protected static ?string $navigationLabel   = 'Actuaciones';
	protected static ?string $modelLabel        = 'Actuación';
	protected static ?string $pluralModelLabel  = 'Actuaciones';
	protected static ?string $slug              = 'actuaciones'; // <- URL /admin/actuaciones

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cliente_id')
                ->options(
                    Cliente::query()
                        ->when(
                            auth()->check() && !auth()->user()->hasRole('admin'),
                            fn($q) => $q->where('usuario_id', auth()->id())
                        )
                        ->pluck('nombre', 'id')
                )
                ->searchable()
                ->required()
                ->label('Cliente'),

            Forms\Components\TextInput::make('codigo')
                ->maxLength(50),

            Forms\Components\DatePicker::make('fecha_inicio')
                ->default(now()),

            Forms\Components\DatePicker::make('fecha_fin'),

            Forms\Components\Select::make('estado')
                ->options([
                    'abierta'     => 'Abierta',
                    'en_proceso'  => 'En proceso',
                    'completada'  => 'Completada',
                ])
                ->default('abierta')
                ->rules(['required', Rule::in(['abierta', 'en_proceso', 'completada'])]),

            Forms\Components\Textarea::make('notas')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('estado')
                    ->badge(),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date('d/m/Y'),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->check() && !auth()->user()->hasRole('admin')) {
            $query->where('usuario_id', auth()->id());
        }
        return $query;
    }

    public static function getRelations(): array
    {
        return [
            ProductosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActuaciones::route('/'),
            'create' => Pages\CreateActuacion::route('/create'),
            'edit'   => Pages\EditActuacion::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool { return true; }
}
