<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TransaccionResource;
use App\Models\AsignacionPulsera;
use App\Models\Pulsera;
use App\Models\Transaccion;
use App\Models\VentasDetalle;
use App\Models\VentasEncabezado;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuscarTransaccion extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document'; // Ícono del menú
    protected static string $view = 'filament.pages.buscar-transacciones'; // Vista personalizada

    protected static ?int $navigationSort = 2; // Orden en el menú
    protected static ?string $navigationLabel = 'Buscar Transacciones';
    protected static ?string $navigationGroup = 'Procesos';

    public $codigo_uid;
    public $transacciones;

    public $total = 0;

    public array $notification = []; // Arreglo para la notificación


    public function buscar()
    {
        $this->notification = [] ;
        $this->total = 0 ;

        $this->transacciones = collect([]) ;

        $bracelet = Pulsera::where('estado', 1)
            ->where(function ($query) {
                $query->where('codigo_uid', $this->codigo_uid)
                    ->orWhere('codigo_serial', $this->codigo_uid);
            })
            ->first();

        if(!$bracelet) {
            $this->notification = [
                'message' => 'No existe este uid dentro del sistema.',
                'color' => 'warning', // Colores de Filament: success, danger, warning, info
            ];
            return  ;
        }

        $assignBracelet = AsignacionPulsera::where('pulsera_id', $bracelet->id)
            ->where('estado', 1)
            ->first();

        if (!$assignBracelet){
            $this->notification = [
                'message' => 'La pulsera no se encuentra asignada a un cliente.',
                'color' => 'warning', // Colores de Filament: success, danger, warning, info
            ];
            return  ;
        }

        $this->transacciones = Transaccion::where('pulsera_id' ,$bracelet->id)
            ->where([
                ['estado', 1],
                ['pagado', 0],
            ])
            ->get();

        $this->total = round($this->transacciones->sum('total'), 2) ;

        if ($this->transacciones->count() === 0) {
            $this->notification = [
                'message' => 'No existen transacciones de esta pulsera.',
                'color' => 'info', // Colores de Filament: success, danger, warning, info
            ];
        }

    }



    public function saveSale()
    {
        if ($this->transacciones->isEmpty()) {

            $this->notification = [
                'message' => 'No existen registros para guardar',
                'color' => 'error', // Colores de Filament: success, danger, warning, info
            ];

            session()->flash('error', 'No hay transacciones para pagar.');
            return;
        }

        try {
            DB::beginTransaction() ;


            $assignBracelet = AsignacionPulsera::where('pulsera_id',$this->transacciones->first()->pulsera_id)
                ->where('estado', 1)->first();

            $transaccionesIdsAll = $this->transacciones->pluck('id')->toArray();


            $salesHeader = VentasEncabezado::create([
                    'pulsera_id' => $this->transacciones->first()->pulsera_id,
                    'user_id' => Auth::user()->id,
                    'total' => round($this->transacciones->sum('total'), 2),
                    'asignacion_pulsera_id' => $assignBracelet->id,
                    'cliente_id' => $assignBracelet->cliente_id,
                    'transacciones_ids' => json_encode($transaccionesIdsAll)
                ]);


            foreach ($this->transacciones as $key => $value) {
                VentasDetalle::create([
                    'cabecera_id' => $salesHeader->id,
                    'cerveza_id' => $value->cerveza_id,
                    'mililitros_consumidos' => $value->mililitros_consumidos,
                    'precio_por_mililitro' => $value->precio_por_mililitro,
                    'total' => $value->total,
                ]);
            }

            $assignBracelet->estado = 2;
            $assignBracelet->fecha_fin_asignacion = now();
            $assignBracelet->save();

            $transactionid = $this->transacciones->pluck('id');
            Transaccion::whereIn('id', $transactionid )->update(['pagado'=> 1]) ;
            $this->transacciones = collect([]);
            $this->total = 0 ;
            DB::commit() ;

            session()->flash('success', 'Pago realizado con éxito.');

            $this->notification = [
                'message' => 'Registros Guardados con éxito',
                'color' => 'success', // Colores de Filament: success, danger, warning, info
            ];

        } catch (\Throwable $th) {
            DB::rollBack() ;

            $this->notification = [
                'message' => $th,
                'color' => 'error', // Colores de Filament: success, danger, warning, info
            ];
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('venta-crear');
    }



    public function mount(): void
    {
        if (!auth()->user()->can('venta-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }


}
