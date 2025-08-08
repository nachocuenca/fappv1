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
use Illuminate\Validation\Rule;
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

            Forms\Components\TextInput::make('serie')->required(),
            Forms\Components\TextInput::make('numero')->numeric()->required(),
            Forms\Components\DatePicker::make('fecha')->default(now())->required(),
            Forms\Components\TextInput::make('base_imponible')->numeric()->required(),
            Forms\Components\TextInput::make('iva_total')->numeric()->required(),
            Forms\Components\TextInput::make('irpf_total')->numeric()->required(),
            Forms\Components\TextInput::make('total')->numeric()->required(),

            Forms\Components\Select::make('estado')
                ->options([
                    'borrador'   => 'Borrador',
                    'confirmado' => 'Confirmado',
                    'servido'    => 'Servido',
                    'cerrado'    => 'Cerrado',
                ])
                ->default('borrador')
                ->rules(['required', Rule::in(['borrador', 'confirmado', 'servido', 'cerrado'])]),

            Forms\Components\Textarea::make('notas')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serie')->sortable(),
                Tables\Columns\TextColumn::make('numero')->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('fecha')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('base_imponible')->money('eur')->sortable(),
                Tables\Columns\TextColumn::make('iva_total')->money('eur')->sortable(),
                Tables\Columns\TextColumn::make('irpf_total')->money('eur')->sortable(),
                Tables\Columns\TextColumn::make('total')->money('eur')->sortable(),
                Tables\Columns\TextColumn::make('estado')->badge()->sortable(),
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

        return auth()->user()?->hasRole('admin') ? $query : $query->mine();
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
