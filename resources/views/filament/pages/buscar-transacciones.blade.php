<x-filament-panels::page>

<style>
    .notification {
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: bold;
        color: white;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
    }

    .notification.success {
        background-color: #22c55e; /* Verde */
        border-left: 4px solid #16a34a;
    }

    .notification.error {
        background-color: #ef4444; /* Rojo */
        border-left: 4px solid #dc2626;
    }

    .notification.warning {
        background-color: #eab308; /* Amarillo */
        border-left: 4px solid #ca8a04;
    }

    .notification.info {
        background-color: #3b82f6; /* Azul */
        border-left: 4px solid #2563eb;
    }
</style>


    @if (collect($promotions)->count() == 0)
        <div style="background-color: #66aaf8 " class="flex items-center gap-2 p-4 text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600" />
            <span>No existen promociones activas, por favor verificar si es necesario crear promociones.</span>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 bg-white rounded shadow">
                <thead class="bg-gray-100">
                    <tr>
                        <th colspan="6" class="px-4 py-2 text-center text-sm font-semibold text-gray-700">
                            Promociones Activas
                        </th>
                    </tr>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Nombre</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Cervezas</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Inicio</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Fin</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Días / Horarios</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($promotions as $promo)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-800">{{ $promo['nombre'] }}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                @foreach ($promo['cervezas'] as $cerveza)
                                    <span class="inline-flex items-center px-2 py-1 mb-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $cerveza['nombre'] }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($promo['fecha_inicio'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                {{ \Carbon\Carbon::parse($promo['fecha_fin'])->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-800">
                                @foreach ($promo['dias'] as $dia)
                                    <div class="mb-1">
                                        <span class="font-semibold capitalize">{{ $dia['dia'] }}:</span>
                                        <span>{{ \Carbon\Carbon::parse($dia['hora_inicio'])->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($dia['hora_fin'])->format('H:i') }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Activa
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    @endif



    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-1">
            <x-filament::input.wrapper class="w-full max-w-md">
                <x-filament::input
                    wire:model.defer="codigo_uid"
                    placeholder="Buscar..."
                    class="rounded-r-none"
                />

                <x-slot name="suffix">
                    <x-filament::icon-button
                        icon="heroicon-m-magnifying-glass"
                        wire:click="buscar"
                        class="rounded-l-none -ml-px"
                        tooltip="Buscar"
                    />
                </x-slot>
            </x-filament::input.wrapper>
        </div>

        <div class="col-span-1 flex justify-end items-center gap-4">

        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Primera columna: Datos del cliente -->
            @php
                $cliente = (object)[
                        'nombre' => 'Juan Pérez',
                        'correo' => 'jasgagsg@gmail.com',
                        'telefono' => '123456789',
            ];
                // Aquí puedes obtener los datos del cliente desde la base de datos o cualquier otra fuente
            @endphp
        <!-- Primera columna: Datos del cliente -->
        <div class="col-span-1 justify-start items-center gap-4 w-full">
            <h3 class="font-semibold text-xl">Datos del Cliente</h3>

            <!-- Bloques de datos del cliente -->
            <div class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <div class="flex justify-between">
                    <p class="text-lg font-medium text-gray-600"><strong>N° Identificación:</strong></p>
                    <p class="text-lg text-gray-800">{{ $formSale->cedula_cliente }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="text-lg font-medium text-gray-600"><strong>Nombre:</strong></p>
                    <p class="text-lg font-medium text-gray-800">{{$formSale->nombre_cliente }} </p>
                </div>
                <div class="flex justify-between">
                    <p class="text-lg font-medium text-gray-600"><strong>Correo:</strong></p>
                    <p class="text-lg text-gray-800">{{ $formSale->email_cliente }}</p>
                </div>
                <!-- Agrega más datos según sea necesario -->
            </div>
        </div>

        <!-- Segunda columna: Valor a pagar, descuento y total -->


        <div class="col-span-1 flex justify-end items-center gap-4 w-full">
            <div class="space-y-2 w-full">
                <h3 class="font-semibold text-lg">Resumen de Pago</h3>

                <!-- Tabla con los detalles de pago -->
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left font-medium text-gray-600">Descripción</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-600">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2">Valor a Pagar</td>
                            <td class="px-4 py-2 text-right">${{ number_format($formSale->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Descuento</td>
                            <td class="px-4 py-2 text-right">${{ number_format($formSale->descuento, 2) }}</td>
                        </tr>
                        <tr class="font-semibold">
                            <td class="px-4 py-2">Total a Pagar</td>
                            <td class="px-4 py-2 text-right">${{ number_format($formSale->total_pagar, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>




    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-1">

        </div>

        <div x-data="{ showModal: @entangle('showModal') }">
            <div class="col-span-1 flex justify-end items-center gap-4 -mt-2">
                <x-filament::button
                    color="success"
                    @click="showModal = true"
                    :disabled="!collect($formSale->ventas_detalles)->count() > 0"
                >
                    Pagar
                </x-filament::button>
            </div>

            <!-- Modal -->
            <div
                x-show="showModal"
                x-transition
                x-cloak
                class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 z-50"
            >
                <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirmar Pago</h2>
                    <p class="text-gray-600 mb-6">¿Estás seguro que deseas confirmar el pago?</p>
                    <div class="flex justify-end gap-2">
                        <x-filament::button color="gray" @click="showModal = false">
                            Cancelar
                        </x-filament::button>
                        <x-filament::button
                        color="success"
                        wire:click="saveSale"
                        wire:loading.attr="disabled"
                        wire:target="saveSale">
                            <span wire:loading.remove wire:target="saveSale">Confirmar</span>
                            <span wire:loading wire:target="saveSale">Procesando...</span>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>




    </div>





    <div wire:loading.flex wire:target="buscar" class="justify-center items-center py-4">
        <div class="flex items-center space-x-2">
            <div class="w-6 h-6 border-4 border-t-transparent border-blue-500 border-solid rounded-full animate-spin"></div>
            <span class="text-gray-700">Cargando...</span>
        </div>
    </div>


    <div wire:loading.remove>


        @if(collect($formSale->ventas_detalles)->count() > 0)

        <div class="relative max-h-[350px] overflow-y-auto border border-gray-300 rounded shadow">

            <div class="overflow-x-auto w-full">
                <!-- Contenedor con altura fija y scroll vertical -->
                <div class="max-h-[300px] overflow-y-auto">
                    <table class="w-full table-auto bg-white border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700">Cerveza</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700 text-right">Mililitros Consumidos</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700 text-right">Precio</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formSale->ventas_detalles as $transaccion)
                            @php
                                $transaccion = (object)$transaccion;
                            @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700">{{ $transaccion->nombre_cerveza }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right">{{ $transaccion->mililitros_consumidos }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right">{{ $transaccion->precio_por_mililitro }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right">${{ $transaccion->total }}</td>
                                </tr>
                            @endforeach


                            @if(collect($formSale->detalle_promocion_aplicada)->count() > 0)
                                @foreach($formSale->detalle_promocion_aplicada as $d)
                                    <tr class="bg-yellow-100 dark:bg-yellow-500 !important">
                                        <td colspan="3" class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right">
                                            {{ $d['descripcion_snapshot'] }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right" style="color:red">
                                            - ${{ number_format($d['total_descuento'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach


                            @endif
                            <!-- Total general -->
                            <tr class="bg-gray-100 font-bold">
                                <td colspan="3" class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right">Total a Pagar</td>
                                <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700 text-right">${{ number_format($formSale->total_pagar, 2) }}</td>
                            </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        @endif

     </div>


</x-filament-panels::page>
