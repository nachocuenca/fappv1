<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetResource\Pages;
use App\Models\Cliente;
use App\Models\Budget;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Presupuestos';
    protected static ?string $pluralLabel = 'Presupuestos';
    protected static ?string $modelLabel = 'Presupuesto';
    protected static ?int $navigationSort = 70;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // ENCABEZADO
            Forms\Components\Section::make('Encabezado')
                ->columns(4)
                ->schema([
                    Forms\Components\Hidden::make('usuario_id')
                        ->default(fn () => auth()->id()),

                    Forms\Components\TextInput::make('serie')
                        ->label('Serie')
                        ->default(fn () => auth()->user()?->serie_presupuesto ?? 'A')
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\TextInput::make('numero')
                        ->label('Número')
                        ->disabled()
                        ->helperText('Se asigna automáticamente al guardar.')
                        ->dehydrated(),

                    Forms\Components\DatePicker::make('fecha')
                        ->label('Fecha')
                        ->default(today())
                        ->required(),
                ])
                ->columnSpanFull(),

            // CLIENTE Y CONDICIONES
            Forms\Components\Section::make('Cliente y condiciones')
                ->columns(6)
                ->schema([
                    Forms\Components\Select::make('cliente_id')
                        ->label('Cliente')
                        ->options(fn () =>
                            Cliente::query()
                                ->where('usuario_id', auth()->id())
                                ->orderBy('nombre')
                                ->pluck('nombre', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->required()
                        ->columnSpan(3),

                    Forms\Components\TextInput::make('validez_dias')
                        ->label('Validez (días)')
                        ->numeric()
                        ->default(30),

                    Forms\Components\Select::make('estado')
                        ->options([
                            'borrador'  => 'Borrador',
                            'enviado'   => 'Enviado',
                            'aceptado'  => 'Aceptado',
                            'rechazado' => 'Rechazado',
                        ])
                        ->default('borrador'),

                    Forms\Components\Toggle::make('activo')
                        ->label('Activo')
                        ->default(true),
                ])
                ->columnSpanFull(),

            // LÍNEAS
            Forms\Components\Section::make('Líneas')
                ->schema([
                    Forms\Components\Repeater::make('lineas')
                        ->relationship('lineas')
                        ->defaultItems(1)
                        ->createItemButtonLabel('Añadir línea')
                        ->columns(12)
                        ->afterStateHydrated(function ($state, Set $set) {
                            static::pushTotalsFromLines(is_array($state) ? $state : [], $set);
                        })
                        ->afterStateUpdated(function ($state, Set $set) {
                            static::pushTotalsFromLines(is_array($state) ? $state : [], $set);
                        })
                        ->schema([
                            // Orden: producto, descripción, cantidad, precio, dto%, IVA%, subtotal
                            Forms\Components\Select::make('producto_id')
                                ->label('Producto')
                                ->options(fn () =>
                                    Producto::query()
                                        ->where('usuario_id', auth()->id())
                                        ->orderBy('nombre')
                                        ->pluck('nombre', 'id')
                                        ->toArray()
                                )
                                ->searchable()
                                ->preload()
                                ->live(onBlur: false)
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    if ($state) {
                                        $p = Producto::find($state);
                                        if ($p) {
                                            if (blank($get('descripcion'))) {
                                                $set('descripcion', $p->nombre);
                                            }
                                            $precio = (float) $p->precio;
                                            $iva    = (float) ($p->iva_porcentaje ?? 21);
                                            $cant   = (float) ($get('cantidad') ?? 1);
                                            $dto    = (float) ($get('descuento_porcentaje') ?? 0);

                                            $set('precio_unitario', number_format($precio, 2, '.', ''));
                                            $set('iva_porcentaje', $iva);
                                            $set('descuento_porcentaje', $dto);

                                            $bruto = $cant * $precio;
                                            $neto  = $bruto - ($bruto * $dto / 100);
                                            $set('subtotal', number_format($neto, 2, '.', ''));
                                        }
                                    }
                                    static::pushTotals($get, $set);
                                })
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('descripcion')
                                ->label('Descripción')
                                ->required()
                                ->columnSpan(4),

                            Forms\Components\TextInput::make('cantidad')
                                ->numeric()->minValue(0.001)->step('0.001')
                                ->default(1)
                                ->live(onBlur: false)
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $precio = (float) ($get('precio_unitario') ?? 0);
                                    $dto    = (float) ($get('descuento_porcentaje') ?? 0);
                                    $bruto  = ((float) $state) * $precio;
                                    $neto   = $bruto - ($bruto * $dto / 100);
                                    $set('subtotal', number_format($neto, 2, '.', ''));
                                    static::pushTotals($get, $set);
                                })
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('precio_unitario')
                                ->label('Precio')
                                ->numeric()->minValue(0)->step('0.01')
                                ->live(onBlur: false)
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $cant  = (float) ($get('cantidad') ?? 1);
                                    $dto   = (float) ($get('descuento_porcentaje') ?? 0);
                                    $bruto = $cant * (float) $state;
                                    $neto  = $bruto - ($bruto * $dto / 100);
                                    $set('subtotal', number_format($neto, 2, '.', ''));
                                    static::pushTotals($get, $set);
                                })
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', ''))
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('descuento_porcentaje')
                                ->label('Dto %')
                                ->numeric()->minValue(0)->maxValue(100)->step('0.01')
                                ->default(0)
                                ->live(onBlur: false)
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $cant  = (float) ($get('cantidad') ?? 1);
                                    $precio= (float) ($get('precio_unitario') ?? 0);
                                    $bruto = $cant * $precio;
                                    $neto  = $bruto - ($bruto * ((float) $state) / 100);
                                    $set('subtotal', number_format($neto, 2, '.', ''));
                                    static::pushTotals($get, $set);
                                })
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('iva_porcentaje')
                                ->label('IVA %')
                                ->numeric()->minValue(0)->maxValue(21)->step('0.01')
                                ->default(21)
                                ->live(onBlur: false)
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    static::pushTotals($get, $set);
                                })
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->disabled()
                                ->dehydrated()
                                ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', ''))
                                ->columnSpan(2),

                            Forms\Components\Textarea::make('notas_producto_linea')
                                ->label('Notas')
                                ->rows(2)
                                ->columnSpan(12),
                        ]),
                ])
                ->columnSpanFull(),

            // TOTALES
            Forms\Components\Section::make('Totales')
                ->columns(5)
                ->schema([
                    Forms\Components\TextInput::make('base_imponible')
                        ->label('Base')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '')),
                    Forms\Components\TextInput::make('descuento_total')
                        ->label('Dto Total')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '')),
                    Forms\Components\TextInput::make('iva_total')
                        ->label('IVA')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '')),
                    Forms\Components\TextInput::make('irpf_total')
                        ->label('IRPF')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '')),
                    Forms\Components\TextInput::make('total')
                        ->label('Total')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($state) => number_format((float) $state, 2, '.', '')),
                ])
                ->columnSpanFull(),

            // NOTAS
            Forms\Components\Section::make('Notas')
                ->schema([
                    Forms\Components\Textarea::make('notas')->rows(4)->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serie')->sortable(),
                Tables\Columns\TextColumn::make('numero')->sortable(),
                Tables\Columns\TextColumn::make('fecha')->date()->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')->label('Cliente')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('base_imponible')->money('EUR'),
                Tables\Columns\TextColumn::make('descuento_total')->money('EUR'),
                Tables\Columns\TextColumn::make('iva_total')->money('EUR'),
                Tables\Columns\TextColumn::make('irpf_total')->money('EUR'),
                Tables\Columns\TextColumn::make('total')->money('EUR'),
                Tables\Columns\TextColumn::make('estado')->badge(),
                Tables\Columns\IconColumn::make('activo')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                ExportAction::make('export')->label('Exportar'),
            ])
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
        ];
    }

    // ================= Helpers internos (SIN archivos externos) =================

    /** Obtiene el array de líneas desde el contexto del Repeater. */
    protected static function linesFrom(Get $get): array
    {
        foreach (['lineas', '../lineas', '../../lineas', '../../../lineas'] as $path) {
            $v = $get($path);
            if (is_array($v)) {
                return $v;
            }
        }
        return [];
    }

    /** Recalcula y vuelca los totales en el formulario, leyendo las líneas desde $get. */
    protected static function pushTotals(Get $get, Set $set): void
    {
        static::pushTotalsFromLines(static::linesFrom($get), $set);
    }

    /** Recalcula y vuelca totales dado un array de líneas. */
    protected static function pushTotalsFromLines(array $lineas, Set $set): void
    {
        [$base, $dto, $iva, $irpf, $total] = static::calcTotals($lineas);

        $set('base_imponible', number_format($base, 2, '.', ''));
        $set('descuento_total', number_format($dto, 2, '.', ''));
        $set('iva_total', number_format($iva, 2, '.', ''));
        $set('irpf_total', number_format($irpf, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }

    /**
     * Cálculo puro de totales (en la propia clase, sin servicios externos).
     * Base = suma de subtotales (netos con dto).
     * Dto total (€) = suma(bruto - subtotal) por línea.
     * IVA total = suma(subtotal * iva% / 100).
     * IRPF total = suma(subtotal * irpf% / 100) — si no hay, se asume 0.
     * Total = Base + IVA - IRPF.
     */
    protected static function calcTotals(array $lineas): array
    {
        $base = 0.0;
        $dtoTotal = 0.0;
        $ivaTotal = 0.0;
        $irpfTotal = 0.0;

        foreach ($lineas as $l) {
            $cantidad = (float) ($l['cantidad'] ?? 0);
            $precio   = (float) ($l['precio_unitario'] ?? 0);
            $dtoPct   = (float) ($l['descuento_porcentaje'] ?? 0);
            $ivaPct   = (float) ($l['iva_porcentaje'] ?? 0);
            $irpfPct  = (float) ($l['irpf_porcentaje'] ?? 0);

            $bruto = $cantidad * $precio;

            $subtotal = isset($l['subtotal']) && $l['subtotal'] !== '' && $l['subtotal'] !== null
                ? (float) $l['subtotal']
                : round($bruto - ($bruto * $dtoPct / 100), 2);

            $base     += $subtotal;
            $dtoTotal += max($bruto - $subtotal, 0);
            $ivaTotal += round($subtotal * $ivaPct / 100, 2);
            $irpfTotal+= round($subtotal * $irpfPct / 100, 2);
        }

        $total = $base + $ivaTotal - $irpfTotal;

        return [round($base, 2), round($dtoTotal, 2), round($ivaTotal, 2), round($irpfTotal, 2), round($total, 2)];
    }
}
