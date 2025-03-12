<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TransaccionResource;
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


    public function buscar()
    {
        $this->transacciones = collect([]) ;
        $bracelet =Pulsera::where([
            ['estado', 1],
            ['codigo_uid', $this->codigo_uid]
        ])->first();

        if(!$bracelet) return ;

        $this->transacciones = Transaccion::where('pulsera_id' ,$bracelet->id)
            ->where([
                ['estado', 1],
                ['pagado', 0],
            ])
            ->get();

        $this->total = round($this->transacciones->sum('total'), 2) ;
    }



    public function saveSale()
    {
        if ($this->transacciones->isEmpty()) {
            session()->flash('error', 'No hay transacciones para pagar.');
            return;
        }

        try {
            DB::beginTransaction() ;

            $salesHeader = VentasEncabezado::create([
                    'pulsera_id' => $this->transacciones->first()->pulsera_id,
                    'user_id' => Auth::user()->id,
                    'total' => round($this->transacciones->sum('total'), 2)
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

            $transactionid = $this->transacciones->pluck('id');
            Transaccion::whereIn('id', $transactionid )->update(['pagado'=> 1]) ;
            $this->transacciones = collect([]);
            $this->total = 0 ;
            DB::commit() ;
            
            session()->flash('success', 'Pago realizado con éxito.');

        } catch (\Throwable $th) {
            DB::rollBack() ;
            dd($th);
        }
    }


}
