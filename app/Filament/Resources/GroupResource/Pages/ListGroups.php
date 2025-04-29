<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(function () {
                return auth()->user()->can('grupo-crear');
            }),
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('grupo-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
