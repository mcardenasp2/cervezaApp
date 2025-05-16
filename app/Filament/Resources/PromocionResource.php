<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromocionDiaRelationManagerResource\RelationManagers\DiasRelationManager;
use App\Filament\Resources\PromocionResource\Pages;
use App\Filament\Resources\PromocionResource\RelationManagers;
use App\Models\Cerveza;
use App\Models\DetallePromocionAplicada;
use App\Models\Promocion;
use Dom\Text;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\View\Components\Modal;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Modal\Action as ModalAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder; // ✅ Correcto

class PromocionResource extends Resource
{
    protected static ?string $model = Promocion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Promociones';

    protected static ?string $navigationGroup = 'Mantenimiento' ;

    public static function getNavigationItems(): array
    {
        $navigationItems = parent::getNavigationItems();

        if (auth()->user()->can('promocion-listar')) {
            return $navigationItems;  // Si tiene el permiso, muestra el recurso
        }

        return [];  // Si no tiene el permiso, no muestra el recurso
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('tipo')
                    ->default('DESCUENTO')
                    ->label('Tipo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->required()
                    ->maxLength(255),
                TextInput::make('pagar')
                    ->numeric()
                    ->label('Pagar')
                    ->required()
                    ->maxLength(255),
                TextInput::make('desde_mililitros')
                    ->numeric()
                    ->label('Desde Mililítros')
                    ->required()
                    ->maxLength(255),
                TextInput::make('hasta_mililitros')
                    ->numeric()
                    ->label('Hasta Mililítros')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('fecha_inicio')
                ->label('Fecha Inicio')
                ->required(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha Fin')
                    ->required(),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->label('Fecha Desde')
                    ->date('Y-m-d H:i:s'),
                TextColumn::make('fecha_fin')
                    ->label('Fecha Hasta')
                    ->date('Y-m-d  H:i:s'),
                TextColumn::make('rango_mililitros')
                    ->label('Rango Mililitros')
                    ->getStateUsing(function ($record) {
                        return $record->desde_mililitros . ' - ' . $record->hasta_mililitros;
                    }),

                TextColumn::make('cervezas.nombre')
                    ->label('Cervezas')
                    ->badge()
                    ->toggleable()
                    ->separator(', '),

                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                        'gray' => 2,
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        0 => 'Inactivo',
                        1 => 'Iniciado',
                        2 => 'Finalizado',
                        default => 'Desconocido'
                    })
            ])
            ->filters([
                Filter::make('estado')
                ->query(fn (Builder $query) => $query->where('estado', '!=', 0))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function () {
                        return auth()->user()->can('promocion-editar');
                    })
                    ->iconButton()
                    ->color('info')

                    ->tooltip('Editar promoción'),
                Tables\Actions\Action::make('asignarCervezas')
                ->visible(function () {
                    return auth()->user()->can('promocion-crear-detalle');
                })
                ->iconButton()
                ->tooltip('Asignar cervezas a la promoción')
                ->color('success')
                ->icon('heroicon-o-plus')
                ->modalHeading('Asignar Cervezas')
                ->modalSubheading(fn ($record) => $record->nombre)
                ->modalSubmitActionLabel('Guardar') // Cambiar el nombre del botón de submit
                ->disabled(fn ($record) => $record->estado !== 1)
                ->modalCancelActionLabel('Cerrar')
                ->form([
                    Select::make('cervezas') // Esto es el campo que tendrá las cervezas seleccionadas
                        ->multiple() // Permite seleccionar varias cervezas
                        ->options(Cerveza::where('estado' ,1 )->get()->pluck('nombre', 'id')) // Recupera todas las cervezas y las asigna
                        ->label('Seleccionar Cervezas')
                        ->default(fn ($record) => $record->cervezas()->pluck('cervezas.id')->toArray())
                        ->preload()
                        ->disableLabel() // Si quieres quitar el label (opcional)
                        ->required(), // Opcional: Si quieres que el campo sea obligatorio
                ])
                ->action(function ($record, array $data) {

                    $record->cervezas()->sync($data['cervezas']);
                            activity()
                    ->performedOn($record)
                    ->causedBy(auth()->user())
                    ->tap(function (\Spatie\Activitylog\Models\Activity $activity){
                        // Aquí personalizamos el log_name y el event
                        $activity->log_name = 'promociones_productos';  // El nombre de tu log, puedes ponerlo como desees
                        $activity->event = 'created';   // Lo que quieres registrar, en este caso 'updated'
                    })
                    ->withProperties([
                        'name' => $record->nombre,
                        'cerveza_id' => $data['cervezas'],
                    ])
                    ->log('Cervezas creados en la promoción');
                    Notification::make()
                        ->title('Cervezas asignadas')
                        ->success()
                        ->send();
                }),
                Tables\Actions\Action::make('asignarDias')
                    // ->visible(fn () => auth()->user()->can('promocion-crear-dias'))
                    ->iconButton()
                    ->tooltip('Asignar días y horarios')
                    ->color('primary')
                    ->icon('heroicon-o-calendar')
                    ->modalHeading('Asignar Días y Horarios')
                    ->modalSubheading(fn ($record) => $record->nombre)
                    ->modalSubmitActionLabel('Guardar')
                    ->modalCancelActionLabel('Cerrar')
                    ->form([
                        Repeater::make('dias')
                        ->default(function ($record) {
                            return $record->dias->map(function ($dia) {
                                return [
                                    'dia' => $dia->dia,
                                    'hora_inicio' => $dia->hora_inicio,
                                    'hora_fin' => $dia->hora_fin,
                                ];
                            })->toArray();
                        })
                        ->schema([
                            Select::make('dia')
                                ->label('Día')
                                ->options([
                                    'lunes' => 'Lunes',
                                    'martes' => 'Martes',
                                    'miércoles' => 'Miércoles',
                                    'jueves' => 'Jueves',
                                    'viernes' => 'Viernes',
                                    'sábado' => 'Sábado',
                                    'domingo' => 'Domingo',
                                ])
                                ->required(),
                            TimePicker::make('hora_inicio')->label('Desde')->required(),
                            TimePicker::make('hora_fin')->label('Hasta')->required(),
                        ])
                        ->rules([
                            function ($get) {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if (!is_array($value)) return;

                                    $dias = array_column($value, 'dia');
                                    $repetidos = array_keys(array_filter(array_count_values($dias), fn ($count) => $count > 1));

                                    if (!empty($repetidos)) {
                                        $fail('No puedes seleccionar días repetidos: ' . implode(', ', $repetidos));
                                    }
                                };
                            },
                        ])
                        ->afterStateHydrated(function ($component, $state) {
                                // Para prevenir que un día ya usado se seleccione otra vez
                                $component->state($state);
                            })
                        ->columns(3)
                        ->minItems(1)
                        ->maxItems(7)
                        ->createItemButtonLabel('Agregar Día')
                        ->label('Días y horarios de promoción'),

                    ])

                    ->action(function ($record, array $data) {
                        $diasData = collect($data['dias'])->map(function ($dia) {
                            return [
                                'dia' => $dia['dia'],
                                'hora_inicio' => $dia['hora_inicio'],
                                'hora_fin' => $dia['hora_fin'],
                            ];
                        });

                        $record->dias()->delete();
                        $record->dias()->createMany($diasData->toArray());

                        Notification::make()
                            ->title('Días y horarios asignados')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('finalizar')
                    ->iconButton()
                    ->tooltip('Finalizar promoción')
                    ->icon('heroicon-o-check')
                    ->color('warning')
                    ->visible(function(){
                        return auth()->user()->can('promocion-finalizar');
                    })
                    ->modalHeading('Finalizar promoción')
                    ->modalDescription('Esta acción no se puede deshacer.')
                    ->requiresConfirmation()
                    ->action(function (Promocion $record) {
                        $record->update(['estado' => 2]);
                        Notification::make()
                            ->title('Promoción finalizada')
                            ->success()
                            ->send();

                    }),
                Tables\Actions\Action::make('eliminar')
                    ->visible(function () {
                        return auth()->user()->can('promocion-eliminar');
                    })
                    ->tooltip('Eliminar promoción')
                    ->iconButton()
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Eliminar promoción')
                    ->modalDescription('Esta acción no se puede deshacer.')
                    ->requiresConfirmation()
                    ->action(function (Promocion $record) {

                        $appliedPromotionDetailTotal = DetallePromocionAplicada::whereHas('venta', function ($query) {
                                $query->where('estado', 1);
                            })
                            ->where('estado', 1)
                            ->where('promocion_id', $record->id)
                            ->count();

                        if ($appliedPromotionDetailTotal > 0) {
                            Notification::make()
                                ->title('No se puede eliminar')
                                ->body('Esta promoción tiene detalles aplicados a ventas activas.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->update(['estado' => 0]);

                        Notification::make()
                            ->title('Promoción eliminada')
                            ->success()
                            ->send();
                    }),


            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
         return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromocions::route('/'),
            'create' => Pages\CreatePromocion::route('/create'),
            'edit' => Pages\EditPromocion::route('/{record}/edit'),
        ];
    }
}
