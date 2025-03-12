<?php

namespace App\Filament\Resources\VentasResource\Pages;

use App\Models\Transaccion;
use App\Models\VentasDetalle;
use App\Models\VentasEncabezado;
use Filament\Facades\Filament;
use Filament\Pages\Page;

use Illuminate\Database\Eloquent\Model;
class BuscarTransacciones extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-search';
    protected static ?string $slug = 'buscar-transacciones';
    protected static string $view = 'filament.resources.ventas-resource.pages.buscar-transacciones';

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $panel = Filament::getCurrentPanel();
        if (!$panel) {
            return url(static::getSlug()); // Devuelve una URL base si no hay panel
        }

        return $panel->generateUrl(static::getSlug(), $parameters, $isAbsolute);

    }

    public $codigo_uid;
    public $transacciones = [];

    public function buscar()
    {
        $this->transacciones = Transaccion::whereHas('pulsera', function ($query) {
            $query->where('codigo_uid', $this->codigo_uid);
        })->where('estado', 0)->get();
    }

    public function pagar($transaccionId)
    {
        $transaccion = Transaccion::find($transaccionId);
        if (!$transaccion) return;

        $ventaEncabezado = VentasEncabezado::create([
            'pulsera_id' => $transaccion->pulsera_id,
            'user_id' => auth()->id(),
            'total' => $transaccion->valor,
            'estado' => 1
        ]);

        VentasDetalle::create([
            'cabecera_id' => $ventaEncabezado->id,
            'cerveza_id' => $transaccion->cerveza_id,
            'mililitros_consumidos' => $transaccion->mililitros_consumidos,
            'precio_por_mililitro' => $transaccion->valor / $transaccion->mililitros_consumidos,
            'total' => $transaccion->valor,
            'estado' => 1
        ]);

        $transaccion->update(['estado' => 1]);

        session()->flash('success', 'Pago realizado correctamente');
        $this->buscar();
    }
}
