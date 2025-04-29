<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $group = $this->record; // Aquí tienes el modelo actualizado

        // Si quieres hacer algo después de guardar, como loguear actividad:
        activity()
            ->performedOn($group)
            ->causedBy(auth()->user())
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) use ($group) {
                // Aquí personalizamos el log_name y el event
                $activity->log_name = 'group_permission';  // El nombre de tu log, puedes ponerlo como desees
                $activity->event = 'created';   // Lo que quieres registrar, en este caso 'updated'
            })
            ->withProperties([
                'name' => $group->name,
                'permissions' => $group->permissions()->pluck('name')->toArray(),
            ])
            ->log('Permisos creados en el grupo');
    }

    public function mount(): void
    {
        parent::mount(); // ⚡ LLAMAS AL MOUNT ORIGINAL DE FILAMENT

        if (!auth()->user()->can('grupo-crear')) {
            abort(403); // Acceso denegado si no tiene el permiso
        }
    }
}
