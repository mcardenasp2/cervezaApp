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

    @if (!empty($notification))
        <div class="notification {{ $notification['color'] }}">
            <p>{{ $notification['message'] }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-1">
            {{ $this->form }}
        </div>

        <div class="col-span-1 flex justify-end items-center gap-4">
              <x-filament::button
                    color="success"
                >
                    Pagar
                </x-filament::button>
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
            <div class="space-y-4 bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between">
                    <p class="text-lg font-medium text-gray-600"><strong>Nombre:</strong></p>
                    <p class="text-lg text-gray-800">{{ $cliente->nombre }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="text-lg font-medium text-gray-600"><strong>Correo:</strong></p>
                    <p class="text-lg text-gray-800">{{ $cliente->correo }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="text-lg font-medium text-gray-600"><strong>Teléfono:</strong></p>
                    <p class="text-lg text-gray-800">{{ $cliente->telefono }}</p>
                </div>
                <!-- Agrega más datos según sea necesario -->
            </div>
        </div>

        <!-- Segunda columna: Valor a pagar, descuento y total -->


        @php
            $valorPagar = 0; // Cambia esto por el valor real
            $descuento = 0; // Cambia esto por el descuento real
            $totalPagar = $valorPagar - $descuento;
        @endphp
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
                            <td class="px-4 py-2 text-right">${{ number_format($valorPagar, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Descuento</td>
                            <td class="px-4 py-2 text-right">${{ number_format($descuento, 2) }}</td>
                        </tr>
                        <tr class="font-semibold">
                            <td class="px-4 py-2">Total a Pagar</td>
                            <td class="px-4 py-2 text-right">${{ number_format($totalPagar, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
                {{-- <x-filament::button color="success">
                    Pagar
                </x-filament::button> --}}


    </div>







    <div class="flex items-center gap-4 mb-4">
        <input
            type="text"
            wire:model="codigo_uid"
            placeholder="Ingrese UID de la pulsera"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button
            wire:click="buscar"
            style="background-color: #3B82F6"
             class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            Buscar
        </button>
        <div class="flex-1 flex justify-end items-center gap-4">
            <span class="text-lg font-semibold">
                Total Pagar: ${{ number_format($total, 2) }}
            </span>
            <button
                wire:click="saveSale"
                style="background-color: #10B981;"
                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                @disabled(!($transacciones && !$transacciones->isEmpty()))
            >
                Pagar
            </button>
        </div>
    </div>

    @if($transacciones && !$transacciones->isEmpty())
        <div class="overflow-x-auto">
            <table class="w-full table-auto bg-white border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700">ID</th>
                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700">Cerveza</th>
                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700">Mililitros Consumidos</th>
                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700">Precio</th>
                        <th class="px-6 py-3 border-b border-gray-200 text-left text-sm font-medium text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transacciones as $transaccion)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700">{{ $transaccion->id }}</td>
                            <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700">{{ $transaccion->cerveza->nombre }}</td>
                            <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700">{{ $transaccion->mililitros_consumidos }}</td>
                            <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700">{{ $transaccion->precio_por_mililitro }}</td>
                            <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-700">{{ $transaccion->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
