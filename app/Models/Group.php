<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Group extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('groups')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'groups';

    protected $fillable = ['name', 'estado'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'group_permission', 'group_id', 'permission_id')->orderBy('name');
    }


}
