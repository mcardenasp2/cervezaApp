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
                ->label('Ir a Clientes')
                ->icon('heroicon-o-user-group')
                ->url(ClienteResource::getUrl()),

            Action::make('pulseras')
                ->label('Ir a Pulseras')
                ->icon('heroicon-o-rectangle-stack')
                ->url(PulseraResource::getUrl()),
            Action::make('asignar')
            ->label('Asignar') // Cambia el texto del botón
            ->icon('heroicon-o-plus-circle')
            ->url(AsignacionPulseraResource::getUrl('asignar-pulsera')),
        ];
    }
}
