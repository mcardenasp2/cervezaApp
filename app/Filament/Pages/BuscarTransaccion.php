<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TransaccionResource;
use App\Models\AsignacionPulsera;
use App\Models\DetallePromocionAplicada;
use App\Models\Promocion;
use App\Models\Pulsera;
use App\Models\Transaccion;
use App\Models\VentasDetalle;
use App\Models\VentasEncabezado;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Actions\Modal\Actions\ButtonAction as ActionsButtonAction;
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

    public $formSale;

    public $promotions ;

    public bool $showModal = false;


    public function __construct() {
        $this->clearFormSale();
    }

    public array $notification = []; // Arreglo para la notificación


    public function getActivePromotions() : array
    {
        Promocion::where('estado', 1)
            ->where('fecha_fin', '<', now())
            ->update(['estado' => 2]);

        return Promocion::with('cervezas')->where('estado', 1)->get()
            ->map(function ($promotions) {
                return [
                    'id' => $promotions->id,
                    'nombre' => $promotions->nombre,
                    'cervezas' => $promotions->cervezas,
                    'fecha_inicio' => $promotions->fecha_inicio,
                    'fecha_fin' => $promotions->fecha_fin,
                    'tipo' => $promotions->tipo,
                    'cantidad' => $promotions->cantidad,
                    'pagar' => $promotions->pagar,
                    'desde_mililitros' => $promotions->desde_mililitros,
                    'hasta_mililitros' => $promotions->hasta_mililitros
                ];
            })->toArray();
    }

    public function clearFormSale() : void
    {
        $this->formSale = (object) [
            'user_id' => null,
            'cliente_id' => null,
            'asignacion_pulsera_id' => null,
            'transacciones_ids' => null,
            'nombre_cliente' => null,
            'cedula_cliente' => null,
            'email_cliente' => null,
            'pulsera_id' => null,
            'total' => 0,
            'descuento' => 0,
            'total_pagar' => 0,
            'ventas_detalles' => [],
            'detalle_promocion_aplicada' =>[]
        ];
    }



    public function buscar()
    {
        $this->clearFormSale() ;
        $this->promotions = $this->getActivePromotions();
        // Buscar las promocoones y si estan activas inactivarlas si ya paso la fecha
        $this->notification = [] ;
        $this->total = 0 ;

        $this->formSale->user_id = Auth::user()->id;
        $this->formSale->ventas_detalles = collect([]);

        $bracelet = Pulsera::where('estado', 1)
            ->where(function ($query) {
                $query->where('codigo_uid', $this->codigo_uid)
                    ->orWhere('codigo_serial', $this->codigo_uid);
            })
            ->first();

        if(!$bracelet) {
            Notification::make()
                ->title('No existe este uid dentro del sistema.')
                ->warning()
                ->send();
            return  ;
        }

        $assignBracelet = AsignacionPulsera::with('cliente')->where('pulsera_id', $bracelet->id)
            ->where('estado', 1)
            ->first();

        if (!$assignBracelet){
            Notification::make()
                ->title('La pulsera no se encuentra asignada a un cliente.')
                ->warning()
                ->send();
            return  ;
        }

        $this->formSale->pulsera_id = $bracelet->id;
        $this->formSale->cliente_id = $assignBracelet->cliente_id;
        $this->formSale->nombre_cliente = $assignBracelet->cliente->nombres;
        $this->formSale->cedula_cliente = $assignBracelet->cliente->cedula;
        $this->formSale->email_cliente = $assignBracelet->cliente->correo;
        $this->formSale->asignacion_pulsera_id = $assignBracelet->id;
        $this->formSale->ventas_detalles = Transaccion::where('pulsera_id' ,$bracelet->id)
            ->where([
                ['estado', 1],
                ['pagado', 0],
            ])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($transaccion) {
                $transaccion->aplica_promocion = false;
                $transaccion->producto_promocionado = false;
                $transaccion->promocion_id = null;
                return $transaccion;
            });
        $this->checkPromotions();





        $this->formSale->total = round($this->formSale->ventas_detalles->sum('total'), 2) ;
        $this->formSale->descuento = round(collect($this->formSale->detalle_promocion_aplicada)->sum('total_descuento'), 2) ;
        $this->formSale->total_pagar = $this->formSale->total - $this->formSale->descuento ;
        $this->formSale->ventas_detalles = $this->formSale->ventas_detalles->map(function($transaccion) {
            return [
                'id' => $transaccion->id,
                'nombre_cerveza' => $transaccion->cerveza->nombre,
                'cerveza_id' => $transaccion->cerveza_id,
                'mililitros_consumidos' => $transaccion->mililitros_consumidos,
                'precio_por_mililitro' => $transaccion->precio_por_mililitro,
                'total' => $transaccion->total,
                'aplica_promocion' => $transaccion->aplica_promocion,
                'producto_promocionado' => $transaccion->producto_promocionado,
                'promocion_id' => $transaccion->promocion_id
            ];
            return $transaccion;
        })->toArray();

        if (collect($this->formSale->ventas_detalles)->count() === 0) {
            Notification::make()
                ->title('No existen transacciones de esta pulsera.')
                ->info()
                ->send();

        }



    }



    public function checkPromotions() : void
    {
        $newPromotions = collect($this->promotions)->flatMap(function ($promotionsItem) {
            $promotionsItem = (object) $promotionsItem;

            return $promotionsItem->cervezas->map(function ($cerveza) use ($promotionsItem) {
                return [
                    'id' => $promotionsItem->id,
                    'nombre' => $promotionsItem->nombre,
                    'cerveza_id' => $cerveza->id,  // Asumimos que quieres agregar el ID de la cerveza
                    'cerveza_nombre' => $cerveza->nombre,  // Nombre de la cerveza
                    'fecha_inicio' => $promotionsItem->fecha_inicio,
                    'fecha_fin' => $promotionsItem->fecha_fin,
                    'tipo' => $promotionsItem->tipo,
                    'cantidad' => $promotionsItem->cantidad,
                    'pagar' => $promotionsItem->pagar,
                    'desde_mililitros' => $promotionsItem->desde_mililitros,
                    'hasta_mililitros' => $promotionsItem->hasta_mililitros,
                ];
            });
        });

        $beerPromotionsGroup = [] ;
        foreach ($newPromotions as $key => $promotionsItem) {
            $promotionsItem = (object) $promotionsItem;

            $details = $this->formSale->ventas_detalles->where('cerveza_id', $promotionsItem->cerveza_id)
                ->where('aplica_promocion', false)
                ->whereBetween('mililitros_consumidos', [$promotionsItem->desde_mililitros, $promotionsItem->hasta_mililitros]);

            $beerPromotionsGroup = [
                'promocion' => $promotionsItem,
                'detalle_promocion' => $details
            ] ;

            $beerPromotionsGroup = (object) $beerPromotionsGroup;
            $groupQuantity = $beerPromotionsGroup->detalle_promocion->count() ;
            $groupQuantity = (int) ($groupQuantity / $beerPromotionsGroup->promocion->cantidad) ;
            $totalProductDiscount = $beerPromotionsGroup->promocion->cantidad - $beerPromotionsGroup->promocion->pagar ;
            $totalDiscountedproducts = $groupQuantity * $totalProductDiscount ;
            $totalPromotionalProducts = $beerPromotionsGroup->promocion->cantidad * $groupQuantity ;
            $idsProductDiscount = $beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalDiscountedproducts)->pluck('id') ;
            $idsProductsPromotion = $beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalPromotionalProducts)->pluck('id') ;

            $this->formSale->ventas_detalles = $this->formSale->ventas_detalles->map(function($transaccion) use ($idsProductDiscount, $idsProductsPromotion, $promotionsItem) {
                if ($idsProductDiscount->contains($transaccion->id)) {
                    $transaccion->aplica_promocion = true;
                    $transaccion->producto_promocionado = true;
                    $transaccion->promocion_id = $promotionsItem->id;
                } elseif ($idsProductsPromotion->contains($transaccion->id)) {
                    $transaccion->aplica_promocion = true;
                    $transaccion->producto_promocionado = false;
                    $transaccion->promocion_id = $promotionsItem->id;
                }
                return $transaccion;
            });

            $this->formSale->detalle_promocion_aplicada[] = [
                'promocion_id' => $promotionsItem->id,
                'cerveza_id' => $promotionsItem->cerveza_id,
                'cantidad_mililitros' => round($beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalPromotionalProducts)->sum('mililitros_consumidos') , 2),
                'cantidad_items_aplicados' => $totalPromotionalProducts,
                'cantidad_gratis' => $totalDiscountedproducts,
                'total_descuento' => round($beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalDiscountedproducts)->sum('total') , 2),
                'descripcion_snapshot' => $promotionsItem->nombre.' - '.$promotionsItem->cerveza_nombre
            ] ;
        }

    }




    public function saveSale()
    {
        $this->showModal = false;
        $this->formSale->ventas_detalles = collect($this->formSale->ventas_detalles) ;
        if ($this->formSale->ventas_detalles->count() === 0) {

            $this->notification = [
                'message' => 'No existen registros para guardar',
                'color' => 'error', // Colores de Filament: success, danger, warning, info
            ];

            session()->flash('error', 'No hay transacciones para pagar.');
            return;
        }

        try {
            DB::beginTransaction() ;


            $assignBracelet = AsignacionPulsera::findOrFail($this->formSale->asignacion_pulsera_id);

            $transaccionesIdsAll = $this->formSale->ventas_detalles->pluck('id')->toArray();


            $salesHeader = VentasEncabezado::create([
                    'pulsera_id' => $this->formSale->pulsera_id,
                    'user_id' => $this->formSale->user_id,
                    'total' => $this->formSale->total,
                    'descuento' => $this->formSale->descuento,
                    'total_pagar' => $this->formSale->total_pagar,
                    'asignacion_pulsera_id' => $this->formSale->asignacion_pulsera_id,
                    'cliente_id' => $this->formSale->cliente_id,
                    'transacciones_ids' => json_encode($transaccionesIdsAll)
                ]);

            foreach ($this->formSale->ventas_detalles as $key => $value) {
                VentasDetalle::create([
                    'cabecera_id' => $salesHeader->id,
                    'cerveza_id' => $value['cerveza_id'],
                    'mililitros_consumidos' => $value['mililitros_consumidos'],
                    'precio_por_mililitro' => $value['precio_por_mililitro'],
                    'total' => $value['total'],
                    'aplica_promocion' => $value['aplica_promocion'],
                    'producto_promocionado' => $value['producto_promocionado'],
                    'promocion_id' => $value['promocion_id']
                ]);
            }

            foreach ($this->formSale->detalle_promocion_aplicada as $key => $value) {
                DetallePromocionAplicada::create([
                    'venta_id' => $salesHeader->id,
                    'promocion_id' => $value['promocion_id'],
                    'cerveza_id' => $value['cerveza_id'],
                    'cantidad_mililitros' => $value['cantidad_mililitros'],
                    'cantidad_items_aplicados' => $value['cantidad_items_aplicados'],
                    'cantidad_gratis' => $value['cantidad_gratis'],
                    'total_descuento' => $value['total_descuento'],
                    'descripcion_snapshot' => $value['descripcion_snapshot']
                ]);
            }

            $assignBracelet->estado = 2;
            $assignBracelet->fecha_fin_asignacion = now();
            $assignBracelet->save();

            $transactionid = $this->formSale->ventas_detalles->pluck('id');

            Transaccion::whereIn('id', $transactionid )->update(['pagado'=> 1]) ;

            $this->clearFormSale() ;

            $this->codigo_uid = null ;

            DB::commit() ;

            session()->flash('success', 'Pago realizado con éxito.');

            Notification::make()
                ->title('Pago realizado con éxito.')
                ->success()
                ->send();

        } catch (\Throwable $th) {
            DB::rollBack() ;

            Notification::make()
                ->title('Error al guardar la venta')
                ->body($th->getMessage())
                ->danger()
                ->send();

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
