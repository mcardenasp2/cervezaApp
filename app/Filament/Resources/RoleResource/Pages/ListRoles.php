<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create-permission') // <--- Usa LinkAction aquí
                ->visible(function () {
                    return auth()->user()->can('rol-crear');
                })
                ->label('Crear Rol')
                ->url(static::getResource()::getUrl('create-permission'))
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];

    }

    public function mount(): void
    {
        if (!auth()->user()->can('rol-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
