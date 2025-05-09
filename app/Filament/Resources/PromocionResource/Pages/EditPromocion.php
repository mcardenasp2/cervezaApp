<?php

namespace App\Filament\Resources\PromocionResource\Pages;

use App\Filament\Resources\PromocionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromocion extends EditRecord
{
    protected static string $resource = PromocionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function mount($record): void
    {
        if (!auth()->user()->can('promocion_editar')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }

        parent::mount($record);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
