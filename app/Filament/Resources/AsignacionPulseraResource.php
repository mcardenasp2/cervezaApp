<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsignacionPulseraResource\Pages;
use App\Filament\Resources\AsignacionPulseraResource\RelationManagers;
use App\Models\AsignacionPulsera;
use App\Models\Transaccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class AsignacionPulseraResource extends Resource
{
    protected static ?string $model = AsignacionPulsera::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Procesos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('cliente.nombres')
                ->label('Cliente')
                ->searchable(),

            TextColumn::make('cliente.cedula')
                ->label('Cédula')
                ->searchable(),

            TextColumn::make('pulsera.codigo_uid')
                ->label('Pulsera UID')
                ->searchable(),

            TextColumn::make('pulsera.codigo_serial')
                ->label('Código Serial')
                ->searchable(),

            TextColumn::make('fecha_creacion')
                ->label('Fecha Creación')
                ->date('d/m/Y')
                ->sortable(),

            TextColumn::make('fecha_inicio_asignacion')
                ->label('Inicio')
                ->dateTime('d/m/Y H:i')
                ->sortable(),

            TextColumn::make('fecha_fin_asignacion')
                ->label('Fin')
                ->dateTime('d/m/Y H:i')
                ->sortable(),

            TextColumn::make('estado')
                ->label('Estado')
                ->formatStateUsing(fn ($state) => match ($state) {
                    0 => 'Inactivo',
                    1 => 'Iniciado',
                    2 => 'Finalizado',
                    default => 'Desconocido'
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    0 => 'danger',
                    1 => 'success',
                    2 => 'gray',
                }),
        ])
        ->defaultSort('fecha_inicio_asignacion', 'desc') // <-- aquí el orden por defecto
        ->filters([
            // puedes agregar filtros aquí si lo deseas
        ])
        ->actions([
            Action::make('eliminar')
                ->label('Eliminar') // El nombre del botón
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action(function ($record) {

                    if($record->estado != 1){
                        Notification::make()
                            ->title('No se puede eliminar')
                            ->body('Esta Pulsera ya ha sido desactivada o Finalizada.')
                            ->danger()
                            ->send();
                        return;

                    }
                    // Verificar si existen transacciones relacionadas
                    $hasTransactions = Transaccion::where([
                            ['pulsera_id', $record->pulsera_id],
                            ['pagado', 0],
                            ['estado', 1],
                        ])->exists();

                    if ($hasTransactions) {
                        // Mostrar una notificación si no se puede eliminar
                        Notification::make()
                            ->title('No se puede eliminar')
                            ->body('Esta Pulsera tiene transacciones relacionadas. No puedes eliminarlo.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Cambiar el estado a inactivo (0)
                    $record->estado = 0;
                    $record->save();

                    // Notificación de éxito
                    Notification::make()
                        ->title('Asignación desactivada')
                        ->body('El cliente ha sido desactivado correctamente.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Confirmar eliminación')
                ->modalSubheading('¿Estás seguro de que deseas desactivar este cliente?')
                ->modalButton('Sí, desactivar'),
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
            'index' => Pages\ListAsignacionPulseras::route('/'),
            // 'create' => Pages\CreateAsignacionPulsera::route('/create'),
            // 'edit' => Pages\EditAsignacionPulsera::route('/{record}/edit'),
            'asignar-pulsera' => Pages\AsignarPulsera::route('/asignar'), // <-- esto
        ];
    }
}
