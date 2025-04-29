<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class CreateRolePermissions extends Page
{
    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.resources.role-resource.pages.create-role-permissions';

    public $name;
    public $guard_name = 'web';
    public $permissions = [];
    public $role_id ;

    protected function getFormSchema(): array
    {
        return [
                // Campos básicos del rol
                TextInput::make('name')
                    ->label('Nombre del Rol')
                    ->required(),

                TextInput::make('guard_name')
                    ->label('Guard Name')
                    ->default('web')
                    ->required(),

                // Acordeón de grupos con permisos
                ...$this->groupPermissionsSchema(),
            ];
    }

    protected function groupPermissionsSchema(): array
    {
        $groups = Group::where('estado', 1)->orderBy('name')->get();

        $schemas = [];

        foreach ($groups as $group) {
            $schemas[] = Section::make($group->name)
                ->schema([
                    CheckboxList::make("permissions")
                        ->label('Permisos')
                        ->options($group->permissions->pluck('name', 'id')->toArray())
                        ->extraAttributes([
                            'style' => 'display: flex; flex-wrap: wrap;',
                        ]),
                ])
                ->collapsible()
                ->collapsed(true);
        }

        return $schemas;
    }


    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function save()
    {

        if (!$this->name || !$this->guard_name) {
            Notification::make()
                ->title('Faltan datos')
                ->body('Debe seleccionar el nombre y el guard.')
                ->danger()
                ->send();

            return;
        }


        try {
            DB::beginTransaction();


            if($this->role_id){
                $role = Role::find($this->role_id);
                $role->update([
                    'name' => $this->name,
                    'guard_name' => $this->guard_name,
                ]);
            }else{
                $role = Role::create([
                    'name' => $this->name,
                    'guard_name' => $this->guard_name,
                ]);

            }


            $role->permissions()->detach();


            if ($this->permissions) {
                foreach ($this->permissions as $permissionId) {

                    $role->permissions()->attach((int)$permissionId);
                }
            }

            DB::commit();

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect(static::getResource()::getUrl('index'));

        } catch (\Throwable $th) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->body($th->getMessage())
                ->danger()
                ->send();
            return;
        }


    }

    public function mount($roleId = null)
    {
        if (!auth()->user()?->can('rol-crear')) {
            abort(403); // Forbidden si no tiene permiso
        }

        $this->form->fill();

        if ($roleId) {

            if (!auth()->user()?->can('rol-editar')) {
                abort(403); // Forbidden si no tiene permiso
            }
            // Si hay un ID de rol, cargar los datos del rol
            $role = Role::with('permissions')->find($roleId);

            if ($role) {
                // Rellenar el formulario con los datos del rol
                $this->name = $role->name;
                $this->guard_name = $role->guard_name;
                $this->permissions = $role->permissions->pluck('id')->toArray();
                $this->role_id = $role->id;
            }
        }

    }


}
