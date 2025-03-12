<?php

namespace App\Filament\Pages;

// use App\Filament\Resources\TransaccionResource;
use Filament\Pages\Page;

class CustomPage extends Page
{
    // protected static string $resource = TransaccionResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document'; // Ícono del menú
    protected static string $view = 'filament.pages.custom-page'; // Vista personalizada

    protected static ?int $navigationSort = 2; // Orden en el menú
    protected static ?string $navigationLabel = 'Prueba';
    protected static ?string $navigationGroup = 'Procesos';
    protected static bool $shouldRegisterNavigation = false; // Oculta el recurso de la navegación
    // protected static string $view = 'filament.resources.transaccion-resource.pages.custom-page';
}
