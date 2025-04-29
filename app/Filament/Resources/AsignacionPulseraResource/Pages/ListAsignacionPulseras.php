<?php

namespace App\Filament\Resources\AsignacionPulseraResource\Pages;

use App\Filament\Resources\AsignacionPulseraResource;
use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\PulseraResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAsignacionPulseras extends ListRecords
{
    protected static string $resource = AsignacionPulseraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clientes')
                ->visible(function () {
                    return auth()->user()->can('cliente-listar');
                })
                ->label('Ir a Clientes')
                ->icon('heroicon-o-user-group')
                ->url(ClienteResource::getUrl()),

            Action::make('pulseras')
                ->visible(function () {
                    return auth()->user()->can('pulsera-listar');
                })
                ->label('Ir a Pulseras')
                ->icon('heroicon-o-rectangle-stack')
                ->url(PulseraResource::getUrl()),
            Action::make('asignar')
            ->visible(function () {
                return auth()->user()->can('asignacion-pulsera-crear');
            })
            ->label('Asignar') // Cambia el texto del botón
            ->icon('heroicon-o-plus-circle')
            ->url(AsignacionPulseraResource::getUrl('asignar-pulsera')),
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('asignacion-pulsera-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
