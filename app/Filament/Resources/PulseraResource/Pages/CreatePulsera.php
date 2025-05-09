<?php

namespace App\Filament\Resources\PulseraResource\Pages;

use App\Filament\Resources\PulseraResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePulsera extends CreateRecord
{
    protected static string $resource = PulseraResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        if (!auth()->user()->can('pulsera-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
