<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VentasResource\Pages;
use App\Filament\Resources\VentasResource\RelationManagers;
use App\Models\Ventas;
use App\Models\VentasDetalle;
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
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;

class VentasResource extends Resource
{
    protected static ?string $model = VentasEncabezado::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Procesos';
    protected static ?string $navigationLabel = 'Ventas';


    public static function getNavigationItems(): array
    {
        $navigationItems = parent::getNavigationItems();

        if (auth()->user()->can('venta-listar')) {
            return $navigationItems;  // Si tiene el permiso, muestra el recurso
        }

        return [];  // Si no tiene el permiso, no muestra el recurso
    }

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
        ->headerActions([
            Tables\Actions\Action::make('Reporte')
                ->visible(function () {
                    return auth()->user()->can('venta-generar-reporte-cuentas-por-dia');
                })
                ->label('Generar Reporte')
                ->modalHeading('Generar Reporte')
                ->modalSubheading('Selecciona un rango de fechas para generar el reporte.')
                ->form([
                    DateTimePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->required()
                        ->format('Y-m-d H:i:s'),  // Formato con fecha y hora

                    DateTimePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->required()
                        ->format('Y-m-d H:i:s'),  // Formato con fecha y hora
                ])
                ->action(function (array $data) {
                    // Aquí manejas la generación de reporte

                    $fechaInicio = Carbon::parse($data['fecha_inicio']);
                    $fechaFin = Carbon::parse($data['fecha_fin']);

                    $ventas = VentasDetalle::with('cerveza')->whereHas('encabezado',function($q) use( $fechaInicio, $fechaFin) {
                        // Filtrar por el rango de fechas en 'created_at' y el estado activo
                        $q->whereBetween('created_at', [$fechaInicio, $fechaFin])
                              ->where('estado', 1); // Filtrar encabezados activos
                    })
                    ->where('estado', 1)
                    ->selectRaw('cerveza_id, SUM(mililitros_consumidos) as total_mililitros, SUM(total) as total_venta')
                    ->groupBy('cerveza_id')
                    ->get() ;

                    $totalMililitros = $ventas->sum('total_mililitros');
                    $totalValor = $ventas->sum('total_venta');
                    // Generar el PDF con los datos
                    $pdf = PDF::loadView('ventas.cuentas_por_dias', [
                        'ventas' => $ventas,
                        'fechaInicio' => $fechaInicio,
                        'fechaFin' => $fechaFin,
                        'totalMililitros' => $totalMililitros,
                        'totalValor' => $totalValor
                    ]);

                    // Descargar el PDF generado
                    return response()->stream(
                        function () use ($pdf) {
                            echo $pdf->output();
                        },
                        200,
                        [
                            "Content-Type" => "application/pdf",
                            "Content-Disposition" => "inline; filename=reporte-ventas.pdf",
                        ]
                    );
                })->modalButton('Generar PDF'),
        ])
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->searchable(),
            TextColumn::make('pulsera.codigo_uid')
                ->label('Código de Pulsera')
                ->sortable()
                ->searchable(),
            TextColumn::make('cliente.nombres')
            ->label('Cliente')
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
            TextColumn::make('descuento')
                ->label('Descuento')
                ->numeric(
                    decimalPlaces: 2,
                    decimalSeparator: '.',
                    thousandsSeparator: ',',
                )
                ->sortable()
                ->searchable(),
            TextColumn::make('total_pagar')
                ->label('Total a Pagar')
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
        ->defaultSort('created_at', 'desc')
        ->filters([
            // Aquí puedes agregar filtros si es necesario
        ])
        ->actions([
            Tables\Actions\Action::make('download')
                ->visible(function () {
                    return auth()->user()->can('venta-reporte');
                })
                ->label('Descargar PDF')
                ->action(function ($record) {
                    $pdf = Pdf::loadView('ventas.vista_pdf', ['record' => $record]);
                    return response()->streamDownload(fn() => print($pdf->output()), 'archivo.pdf');
                })
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
            'index' => Pages\ListVentas::route('/'),


        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
