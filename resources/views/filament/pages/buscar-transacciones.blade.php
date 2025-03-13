<x-filament-panels::page>
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
