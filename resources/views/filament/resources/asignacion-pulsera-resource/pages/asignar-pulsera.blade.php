<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <x-filament::button wire:click="save">
            Guardar Asignación
        </x-filament::button>
    </div>
</x-filament-panels::page>
