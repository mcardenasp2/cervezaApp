<?php

namespace App\Filament\Resources\TransaccionResource\Pages;

use App\Filament\Resources\TransaccionResource;
use App\Models\Transaccion;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class BuscarTransaccion extends Page
{
    protected static string $resource = TransaccionResource::class;
    protected static string $view = 'filament.resources.transaccion-resource.pages.buscar-transaccion';

    public static function getNavigationLabel(): string
    {
        return 'Buscar Transacciones';
    }

    public static function getNavigationGroup(): string
    {
        return 'Transacciones'; // 🔥 Se agrupará bajo este nombre en el menú
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true; // 🔥 Esto asegura que la página aparezca en el menú
    }

    public $codigo_uid;
    public Collection $transacciones;

    public function mount()
    {
        $this->transacciones = collect();
    }

    public function buscar()
    {
        $this->transacciones = Transaccion::where('codigo_uid', 'like', "%{$this->codigo_uid}%")->get();
    }


}
