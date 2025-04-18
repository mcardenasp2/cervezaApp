<?php

namespace App\Filament\Resources\AsignacionPulseraResource\Pages;

use App\Filament\Resources\AsignacionPulseraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsignacionPulsera extends EditRecord
{
    protected static string $resource = AsignacionPulseraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
