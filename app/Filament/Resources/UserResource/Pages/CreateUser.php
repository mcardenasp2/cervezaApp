<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function mount(): void
    {
        parent::mount(); // ⚡ LLAMAS AL MOUNT ORIGINAL DE FILAMENT

        if (!auth()->user()->can('usuario-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
