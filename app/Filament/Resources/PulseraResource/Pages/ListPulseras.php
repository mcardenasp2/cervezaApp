<?php

namespace App\Filament\Resources\PulseraResource\Pages;

use App\Filament\Resources\PulseraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPulseras extends ListRecords
{
    protected static string $resource = PulseraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
