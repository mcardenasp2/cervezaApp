<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function afterSave(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount($record): void
    {
        // Aquí estamos verificando el permiso antes de permitir la edición
        if (!auth()->user()->can('usuario-editar')) {
            abort(403); // Si no tiene el permiso, denegamos el acceso
        }

        // Llamar al método mount del padre para que Filament lo procese correctamente
        parent::mount($record);
    }


}
