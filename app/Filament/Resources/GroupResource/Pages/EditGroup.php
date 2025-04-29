<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function afterSave(): void
    {
        $group = $this->record; // Aquí tienes el modelo actualizado
        // Si quieres hacer algo después de guardar, como loguear actividad:
        activity()
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) use ($group) {
                // Aquí personalizamos el log_name y el event
                $activity->log_name = 'group_permission';  // El nombre de tu log, puedes ponerlo como desees
                $activity->event = 'updated';   // Lo que quieres registrar, en este caso 'updated'
            })
            ->withProperties([
                'name' => $group->name,
                'permissions' => $group->permissions()->pluck('name')->toArray(),
            ])
            ->log('Permisos actualizados en el grupo');
    }

    public function mount($record): void
    {
        // Aquí estamos verificando el permiso antes de permitir la edición
        if (!auth()->user()->can('grupo-editar')) {
            abort(403); // Si no tiene el permiso, denegamos el acceso
        }

        // Llamar al método mount del padre para que Filament lo procese correctamente
        parent::mount($record);
    }
}
