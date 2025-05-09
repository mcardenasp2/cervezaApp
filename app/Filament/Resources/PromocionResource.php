<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromocionResource\Pages;
use App\Filament\Resources\PromocionResource\RelationManagers;
use App\Models\Cerveza;
use App\Models\Promocion;
use Dom\Text;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\View\Components\Modal;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromocionResource extends Resource
{
    protected static ?string $model = Promocion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Promociones';

    protected static ?string $navigationGroup = 'Mantenimiento' ;

    public static function getNavigationItems(): array
    {
        $navigationItems = parent::getNavigationItems();

        if (auth()->user()->can('promocion_listar')) {
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
                    ->label('Desde')
                    ->required()
                    ->maxLength(255),
                TextInput::make('hasta_mililitros')
                    ->numeric()
                    ->label('Hasta')
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
                    ->date('d/m/Y'),
                TextColumn::make('fecha_fin')
                    ->label('Fecha Hasta')
                    ->date('d/m/Y'),
                TextColumn::make('rango_mililitros')
                    ->label('Rango Mililitros')
                    ->getStateUsing(function ($record) {
                        return $record->desde_mililitros . ' - ' . $record->hasta_mililitros;
                    }),
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 1,
                        'danger' => 0,
                    ])
                    ->formatStateUsing(fn ($state) => $state == 1 ? 'Activo' : 'Inactivo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function () {
                        return auth()->user()->can('promocion_editar');
                    }),
                Tables\Actions\Action::make('asignarCervezas')
                ->visible(function () {
                    return auth()->user()->can('promocion_crear_detalle');
                })
                ->label('Asignar')
                ->icon('heroicon-o-eye')
                ->modalHeading('Asignar Cervezas')
                ->modalSubheading(fn ($record) => $record->nombre)
                ->modalSubmitAction(function ($record, $data) {

                })
                ->modalSubmitActionLabel('Guardar') // Cambiar el nombre del botón de submit

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
