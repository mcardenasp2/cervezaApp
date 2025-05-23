<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pulsera extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('pulseras')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'pulseras';

    protected $fillable = ['codigo_serial', 'codigo_uid', 'estado'];

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }
}
