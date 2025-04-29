<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(function () {
                return auth()->user()->can('permisos-crear');
            }),
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('permisos-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
