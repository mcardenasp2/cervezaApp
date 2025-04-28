<?php

namespace App\Filament\Resources\PulseraResource\Pages;

use App\Filament\Resources\PulseraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPulsera extends EditRecord
{
    protected static string $resource = PulseraResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount($record): void
    {
        // Aquí estamos verificando el permiso antes de permitir la edición
        if (!auth()->user()->can('pulsera-editar')) {
            abort(403); // Si no tiene el permiso, denegamos el acceso
        }
        // Llamar al método mount del padre para que Filament lo procese correctamente
        parent::mount($record);
    }
}
