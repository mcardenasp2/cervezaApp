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
}
