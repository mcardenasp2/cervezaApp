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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Actions\Modal\Actions\ButtonAction as ActionsButtonAction;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
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
            ->where('fecha_fin', '<', date('Y-m-d'))
            ->update(['estado' => 2]);

        return Promocion::with('cervezas', 'dias')->where('estado', 1)->get()
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
                    'dias' => $promotions->dias,
                    'desde_mililitros' => $promotions->desde_mililitros,
                    'hasta_mililitros' => $promotions->hasta_mililitros,

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
        $newPromotions = collect($this->promotions)->map(function ($promotionsItem) {
            return [
                'id' => $promotionsItem['id'],
                'nombre' => $promotionsItem['nombre'],
                'cervezas_ids' => $promotionsItem['cervezas']->pluck('id')->toArray(),
                'cervezas_nombres' => $promotionsItem['cervezas']->pluck('nombre')->toArray(),
                'fecha_inicio' => $promotionsItem['fecha_inicio'],
                'fecha_fin' => $promotionsItem['fecha_fin'],
                'tipo' => $promotionsItem['tipo'],
                'cantidad' => $promotionsItem['cantidad'],
                'pagar' => $promotionsItem['pagar'],
                'dias' => $promotionsItem['dias'],
                'desde_mililitros' => $promotionsItem['desde_mililitros'],
                'hasta_mililitros' => $promotionsItem['hasta_mililitros'],
            ];
        })
        ->sortByDesc('cantidad');

        $beerPromotionsGroup = [] ;

        $startDate = new DateTime(date('Y-m-d'));
        $startDate = $startDate->modify('-2 day');
        $startDate = $startDate->format('Y-m-d');


        foreach ($newPromotions as $key => $promotionsItem) {
            $promotionsItem['fecha_inicio'] = $startDate;
            $promotionsItem['fecha_fin'] = date('Y-m-d') ;
            $promotionalDates = $this->generatePromotionalDates($promotionsItem) ;

            foreach ($promotionalDates as $key2 => $value) {


                $promotionsItem = (object) $promotionsItem;

                $details = $this->formSale->ventas_detalles->whereIn('cerveza_id', $promotionsItem->cervezas_ids)
                    ->where('aplica_promocion', false)
                    ->whereBetween('created_at', [$value['fecha_inicio'], $value['fecha_fin']])
                    ->whereBetween('mililitros_consumidos', [$promotionsItem->desde_mililitros, $promotionsItem->hasta_mililitros]);

                if($details->count() > 0){

                    $beerPromotionsGroup = [
                        'promocion' => $promotionsItem,
                        'detalle_promocion' => $details
                    ] ;

                    $beerPromotionsGroup = (object) $beerPromotionsGroup;
                    $groupQuantity = $beerPromotionsGroup->detalle_promocion->count() ;
                    // Obtengo la cantidad de grupos de productos que se pueden aplicar la promocion
                    $groupQuantity = $beerPromotionsGroup->promocion->cantidad > 0
                                    ? (int) ($groupQuantity / $beerPromotionsGroup->promocion->cantidad)
                                    : 0;
                    // Obtengo la cantidad de productos que se pueden aplicar la promocion
                    $totalProductDiscount = $beerPromotionsGroup->promocion->cantidad - $beerPromotionsGroup->promocion->pagar ;
                    //Obtengo el total de productos que se pueden aplicar la promocion
                    $totalDiscountedproducts = $groupQuantity * $totalProductDiscount ;
                    // Cantidad de productos que se evaluaron para promocion
                    $totalPromotionalProducts = $beerPromotionsGroup->promocion->cantidad * $groupQuantity ;
                    // Ids de lod productos a descontar
                    $idsProductDiscount = $beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalDiscountedproducts)->pluck('id') ;
                    // ids de los productos a evaluar para promocion
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

                    $totalDiscount = round($beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalDiscountedproducts)->sum('total') , 2);

                    if ($totalDiscount > 0) {
                        $this->formSale->detalle_promocion_aplicada[] = [
                            'promocion_id' => $promotionsItem->id,
                            'cervezas_ids' => json_encode($promotionsItem->cervezas_ids),
                            'cantidad_mililitros' => round($beerPromotionsGroup->detalle_promocion->sortBy('mililitros_consumidos')->take($totalPromotionalProducts)->sum('mililitros_consumidos') , 2),
                            'cantidad_items_aplicados' => $totalPromotionalProducts,
                            'cantidad_gratis' => $totalDiscountedproducts,
                            'cantidad_promociones' => $groupQuantity ,
                            'total_descuento' => $totalDiscount,
                            'descripcion_snapshot' => $groupQuantity.' - '.$promotionsItem->nombre.' ('.implode(', ', $promotionsItem->cervezas_nombres).')'
                        ] ;
                    }
                }
            }


        }

    }



    public function generatePromotionalDates( array $promotion) : array
    {
        $fechaInicio = Carbon::parse($promotion['fecha_inicio']);
        $fechaFin = Carbon::parse($promotion['fecha_fin']);
        $diasConfigurados = $promotion['dias']; // Colección de PromocionDia

        $periodo = CarbonPeriod::create($fechaInicio, $fechaFin);

        $fechasPromocion = [];

        foreach ($periodo as $fecha) {
            $diaSemana = strtolower($fecha->locale('es')->isoFormat('dddd')); // Ej: 'lunes'

            // Verifica si este día está en los días configurados
            $diaConfig = $diasConfigurados->first(fn ($d) => strtolower($d->dia) === $diaSemana);

            if ($diaConfig) {
                $horaInicio = $diaConfig->hora_inicio;
                $horaFin = $diaConfig->hora_fin;

                $fechaInicioCompleta = $fecha->copy()->setTimeFromTimeString($horaInicio);
                $fechaFinCompleta = $fecha->copy()->setTimeFromTimeString($horaFin);

                // Si la hora de fin es menor o igual, asumimos que cruza al siguiente día
                if ($horaFin <= $horaInicio) {
                    $fechaFinCompleta->addDay();
                }

                $fechasPromocion[] = [
                    'fecha_inicio' => $fechaInicioCompleta->format('Y-m-d H:i:s'),
                    'fecha_fin' => $fechaFinCompleta->format('Y-m-d H:i:s'),
                    'dia' => ucfirst($diaSemana),
                ];
            }
        }

        return $fechasPromocion ;
    }




    public function saveSale()
    {
        $this->showModal = false;
        $this->formSale->ventas_detalles = collect($this->formSale->ventas_detalles) ;
        if ($this->formSale->ventas_detalles->count() === 0) {
            Notification::make()
                ->title('No existen transacciones para pagar.')
                ->danger()
                ->send();


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
                    'cervezas_ids' => $value['cervezas_ids'],
                    'cantidad_mililitros' => $value['cantidad_mililitros'],
                    'cantidad_items_aplicados' => $value['cantidad_items_aplicados'],
                    'cantidad_gratis' => $value['cantidad_gratis'],
                    'cantidad_promociones' => $value['cantidad_promociones'],
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
        $this->promotions = $this->getActivePromotions();

        if (!auth()->user()->can('venta-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }



}
