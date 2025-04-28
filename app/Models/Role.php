<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    use HasFactory;
    use HasRoles, LogsActivity;


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('roles')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'roles' ;

    protected $fillable = ['name', 'guard_name'] ;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }


}
