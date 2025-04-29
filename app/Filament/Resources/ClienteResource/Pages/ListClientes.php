<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ListRecords;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(function () {
                // Solo mostrar el botón "Crear" si el usuario tiene el permiso 'usuario-crear'
                return auth()->user()->can('cliente-crear');
            }),
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('cliente-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }

}
