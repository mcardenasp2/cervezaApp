<?php

namespace App\Filament\Resources\CervezaResource\Pages;

use App\Filament\Resources\CervezaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCerveza extends EditRecord
{
    protected static string $resource = CervezaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
