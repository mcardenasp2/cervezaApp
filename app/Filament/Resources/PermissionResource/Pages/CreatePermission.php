<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount(); // ⚡ LLAMAS AL MOUNT ORIGINAL DE FILAMENT

        if (!auth()->user()->can('permisos-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
