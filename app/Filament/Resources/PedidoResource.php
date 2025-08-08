<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Models\Pedido;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('cliente_id')
                ->label('Cliente')
                ->options(
                    Cliente::query()
                        ->when(auth()->check() && !auth()->user()->hasRole('admin'), fn($q) => $q->where('usuario_id', auth()->id()))
                        ->pluck('nombre', 'id')
                )
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('codigo')
                ->maxLength(50),

            Forms\Components\DatePicker::make('fecha')->default(now())->required(),

            Forms\Components\Select::make('estado')
                ->options([
                    'borrador'  => 'Borrador',
                    'abierto'   => 'Abierto',
                    'cerrado'   => 'Cerrado',
                ])->default('borrador'),

            Forms\Components\Textarea::make('notas')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')->label('Código')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('fecha')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('estado')->badge()->sortable(),
                Tables\Columns\TextColumn::make('total')->money('eur')->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit'   => Pages\EditPedido::route('/{record}/edit'),
        ];
    }
	
	public static function canCreate(): bool { return true; }
}
