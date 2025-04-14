<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cliente extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('clientes')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'clientes';

    protected $fillable = [
        'cedula',
        'nombres',
        'estado',
        'correo'
    ];
}
