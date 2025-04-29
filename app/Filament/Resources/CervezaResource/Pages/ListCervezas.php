<?php

namespace App\Filament\Resources\CervezaResource\Pages;

use App\Filament\Resources\CervezaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCervezas extends ListRecords
{
    protected static string $resource = CervezaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(function () {
                return auth()->user()->can('cerveza-crear');
            }),
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('cerveza-listar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
