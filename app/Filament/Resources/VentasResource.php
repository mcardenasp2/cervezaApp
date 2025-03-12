<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentasResource\Pages;
use App\Filament\Resources\VentasResource\RelationManagers;
use App\Models\Ventas;
use App\Models\VentasEncabezado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;

class VentasResource extends Resource
{
    protected static ?string $model = VentasEncabezado::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Procesos';
    protected static ?string $navigationLabel = 'Ventas';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Aquí puedes agregar otras acciones si es necesario
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->searchable(),
            TextColumn::make('pulsera.codigo_uid')
                ->label('Código de Pulsera')
                ->sortable()
                ->searchable(),
            TextColumn::make('usuario.name')
                ->label('Usuario')
                ->sortable()
                ->searchable(),
            TextColumn::make('total')
                ->label('Total')
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                )
                ->sortable()
                ->searchable(),
            BadgeColumn::make('estado')
            ->label('Estado')
            ->colors([
                'success' => 1,   // Verde para Activo
                'danger' => 0,    // Rojo para Inactivo
            ])
            ->formatStateUsing(fn ($state) => $state == 1 ? 'Activo' : 'Inactivo'),
            TextColumn::make('created_at')
                ->label('Fecha de Creación')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->searchable(),
        ])
        ->filters([
            // Aquí puedes agregar filtros si es necesario
        ])
        ->actions([
            Tables\Actions\Action::make('download')
                ->label('Descargar PDF')
                ->action(function ($record) {
                    $pdf = Pdf::loadView('ventas.vista_pdf', ['record' => $record]);
                    return response()->streamDownload(fn() => print($pdf->output()), 'archivo.pdf');
                })
        ])
        ->bulkActions([
            // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVentas::route('/'),
            

        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
