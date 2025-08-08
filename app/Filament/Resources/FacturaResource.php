<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacturaResource\Pages;
use App\Models\Factura;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Illuminate\Database\Eloquent\Builder;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

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
                ->rules(['required', 'numeric']),

            Forms\Components\TextInput::make('serie')
                ->maxLength(20)
                ->required(),

            Forms\Components\TextInput::make('numero')
                ->required()
                ->numeric(),

            Forms\Components\DatePicker::make('fecha')
                ->required(),

            Forms\Components\Select::make('estado')
                ->options([
                    'borrador' => 'Borrador',
                    'enviado' => 'Enviado',
                    'pagado' => 'Pagado',
                ])
                ->default('borrador')
                ->rules(['required', Rule::in(['borrador', 'enviado', 'pagado'])]),

            Forms\Components\TextInput::make('base_imponible')
                ->required()
                ->numeric(),

            Forms\Components\TextInput::make('iva_total')
                ->required()
                ->numeric(),

            Forms\Components\TextInput::make('irpf_total')
                ->required()
                ->numeric(),

            Forms\Components\TextInput::make('total')
                ->required()
                ->numeric(),
        ]);
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return auth()->user()?->hasRole('admin') ? $query : $query->mine();
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
