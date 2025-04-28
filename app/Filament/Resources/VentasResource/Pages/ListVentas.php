<?php

namespace App\Filament\Resources\VentasResource\Pages;

use App\Filament\Resources\VentasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVentas extends ListRecords
{
    protected static string $resource = VentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function mount(): void
    {
        if (!auth()->user()->can('venta-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
