<x-filament-panels::page>
    <div>
        <input type="text" wire:model="codigo_uid" placeholder="Ingrese UID de la pulsera">
        <button wire:click="buscar">Buscar</button>
    </div>

    @if($transacciones)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Valor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transacciones as $transaccion)
                    <tr>
                        <td>{{ $transaccion->id }}</td>
                        <td>{{ $transaccion->valor }}</td>
                        <td>
                            <button wire:click="pagar({{ $transaccion->id }})">Pagar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</x-filament-panels::page>
