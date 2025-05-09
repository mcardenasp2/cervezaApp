<?php

namespace App\Filament\Resources\PromocionResource\Pages;

use App\Filament\Resources\PromocionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromocions extends ListRecords
{
    protected static string $resource = PromocionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function () {
                    return auth()->user()->can('promocion_crear');
                }),
        ];
    }

    public function mount(): void
    {
        if(!auth()->user()->can('promocion_listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
