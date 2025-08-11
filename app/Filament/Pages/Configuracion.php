<?php

namespace App\Filament\Pages;

use App\Models\Serie;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class Configuracion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configuración';
    protected static ?string $navigationGroup = 'Configuración';
    protected static string $view = 'filament.pages.configuracion';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();

        $series = [];
        foreach (['factura', 'abono', 'presupuesto'] as $tipo) {
            $serie = Serie::firstOrCreate(
                ['usuario_id' => $user->id, 'tipo' => $tipo],
                ['serie' => 'A', 'siguiente_numero' => 1, 'reinicio_anual' => false]
            );
            $series[] = $serie->only(['id', 'tipo', 'serie', 'siguiente_numero', 'reinicio_anual']);
        }

        $templates = [];
        foreach (['factura', 'abono', 'presupuesto'] as $tipo) {
            $setting = Setting::firstOrCreate(
                ['user_id' => $user->id, 'key' => 'template_' . $tipo],
                ['value' => '']
            );
            $templates[$tipo] = $setting->value;
        }

        $this->form->fill([
            'company_name' => $user->company_name,
            'logo_path' => $user->logo_path,
            'series' => $series,
            'templates' => $templates,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('configTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Datos')
                            ->schema([
                                Forms\Components\TextInput::make('company_name')
                                    ->label('Nombre')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Logotipo')
                                    ->image()
                                    ->directory('logos'),
                                Forms\Components\TextInput::make('password')
                                    ->label('Nueva contraseña')
                                    ->password()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->label('Confirmar contraseña')
                                    ->password()
                                    ->dehydrated(false),
                            ]),
                        Forms\Components\Tabs\Tab::make('Series')
                            ->schema([
                                Forms\Components\Repeater::make('series')
                                    ->schema([
                                        Forms\Components\Hidden::make('id'),
                                        Forms\Components\TextInput::make('tipo')
                                            ->disabled(),
                                        Forms\Components\TextInput::make('serie')
                                            ->label('Prefijo')
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('siguiente_numero')
                                            ->label('Numeración')
                                            ->numeric(),
                                        Forms\Components\Toggle::make('reinicio_anual')
                                            ->label('Reinicio anual'),
                                    ])
                                    ->columns(5),
                            ]),
                        Forms\Components\Tabs\Tab::make('Plantillas')
                            ->schema([
                                Forms\Components\Textarea::make('templates.factura')
                                    ->label('Factura'),
                                Forms\Components\Textarea::make('templates.abono')
                                    ->label('Abono'),
                                Forms\Components\Textarea::make('templates.presupuesto')
                                    ->label('Presupuesto'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $user->company_name = $data['company_name'] ?? null;
        $user->logo_path = $data['logo_path'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        foreach ($data['series'] ?? [] as $serieData) {
            Serie::updateOrCreate(
                ['id' => $serieData['id'] ?? null],
                [
                    'usuario_id' => $user->id,
                    'tipo' => $serieData['tipo'],
                    'serie' => $serieData['serie'] ?? 'A',
                    'siguiente_numero' => $serieData['siguiente_numero'] ?? 1,
                    'reinicio_anual' => $serieData['reinicio_anual'] ?? false,
                ]
            );
        }

        foreach ($data['templates'] ?? [] as $tipo => $contenido) {
            Setting::updateOrCreate(
                ['user_id' => $user->id, 'key' => 'template_' . $tipo],
                ['value' => $contenido]
            );
        }

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar')
                ->submit('save'),
        ];
    }
}
