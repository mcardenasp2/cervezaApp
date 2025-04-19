<?php

namespace App\Filament\Resources\AsignacionPulseraResource\Pages;

use App\Filament\Resources\AsignacionPulseraResource;
use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\PulseraResource;
use App\Models\AsignacionPulsera;
use App\Models\Cliente;
use App\Models\Pulsera;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class AsignarPulsera extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AsignacionPulseraResource::class;

    protected static string $view = 'filament.resources.asignacion-pulsera-resource.pages.asignar-pulsera';

    public $cliente_id;
    public $pulsera_id;


    public function getHeaderActions(): array
    {
        return [
            Action::make('clientes')
                ->label('Ir a Clientes')
                ->icon('heroicon-o-user-group')
                ->url(ClienteResource::getUrl()),

            Action::make('pulseras')
                ->label('Ir a Pulseras')
                ->icon('heroicon-o-rectangle-stack')
                ->url(PulseraResource::getUrl()),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('cliente_id')
                ->label('Buscar Cliente')
                ->searchable()
                ->getSearchResultsUsing(fn (string $search) =>
                    Cliente::where('estado', 1)
                        ->where(function ($q) use ($search) {
                            $q->where('cedula', 'like', "%{$search}%")
                            ->orWhere('nombres', 'like', "%{$search}%");
                        })
                        ->limit(10)
                        ->get()
                        ->mapWithKeys(fn ($cliente) => [
                            $cliente->id => "{$cliente->cedula} - {$cliente->nombres}"
                        ])
                        ->toArray()
                )
                ->getOptionLabelUsing(fn ($value): ?string =>
                    optional(Cliente::find($value))->cedula . ' - ' . optional(Cliente::find($value))->nombres
                ),

                Select::make('pulsera_id')
                    ->label('Buscar Pulsera')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        $ids = AsignacionPulsera::where('estado', 1)->pluck('pulsera_id');

                        return Pulsera::whereNotIn('id', $ids)
                            ->where('estado', 1)
                            ->where(function ($query) use ($search) {
                                $query->where('codigo_uid', 'like', "%{$search}%")
                                    ->orWhere('codigo_serial', 'like', "%{$search}%");
                            })
                            ->limit(10)
                            ->get()
                            ->mapWithKeys(function ($pulsera) {
                                $label = "{$pulsera->codigo_uid} - {$pulsera->codigo_serial}";
                                return [$pulsera->id => $label];
                            });
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $pulsera = \App\Models\Pulsera::find($value);
                        if (!$pulsera) return null;

                        return "{$pulsera->codigo_uid} - {$pulsera->codigo_serial}";
                    }),
        ];
    }

    public function mount()
    {
        $this->form->fill();
    }

    public function save()
    {
        if (!$this->cliente_id || !$this->pulsera_id) {
            Notification::make()
                ->title('Faltan datos')
                ->body('Debe seleccionar un cliente y una pulsera.')
                ->danger()
                ->send();

            return;
        }

        $braceletAssignment = AsignacionPulsera::where('pulsera_id', $this->pulsera_id)
            ->where('estado', 1)->first();

        if ($braceletAssignment) {
            Notification::make()
                ->title('Pulsera ya asignada')
                ->body('La pulsera ya está asignada a otro cliente.')
                ->danger()
                ->send();
            return;
        }

        $client = Cliente::find($this->cliente_id);

        if (!$client) {
            Notification::make()
                ->title('Cliente no encontrado')
                ->body('El cliente seleccionado no existe.')
                ->danger()
                ->send();
            return;
        }

        AsignacionPulsera::create([
            'cliente_id' => $this->cliente_id,
            'pulsera_id' => $this->pulsera_id,
            'usuario_id' => auth()->id(),
            'fecha_creacion' => date('Y-m-d'),
            'fecha_inicio_asignacion' => now(),
        ]);
        Notification::make()
            ->title('Éxito')
            ->body('La pulsera fue asignada correctamente.')
            ->success()
            ->send();
        // Opcional: Limpiar los campos
        $this->reset(['cliente_id', 'pulsera_id']);
        // O redirigir a otra página
        return redirect()->to(AsignacionPulseraResource::getUrl());

    }


}
