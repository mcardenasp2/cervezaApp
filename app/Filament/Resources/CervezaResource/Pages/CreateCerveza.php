<?php

namespace App\Filament\Resources\CervezaResource\Pages;

use App\Filament\Resources\CervezaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCerveza extends CreateRecord
{
    protected static string $resource = CervezaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        if (!auth()->user()->can('cerveza-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
