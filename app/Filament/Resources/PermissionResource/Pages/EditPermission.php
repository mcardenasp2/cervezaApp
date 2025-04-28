<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount($record): void
    {
        // Aquí estamos verificando el permiso antes de permitir la edición
        if (!auth()->user()->can('permisos-editar')) {
            abort(403); // Si no tiene el permiso, denegamos el acceso
        }

        // Llamar al método mount del padre para que Filament lo procese correctamente
        parent::mount($record);
    }
}
