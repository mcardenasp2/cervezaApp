<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function () {
                    return auth()->user()->can('usuario-crear');
                }),
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('usuario-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
