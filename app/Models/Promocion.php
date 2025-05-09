<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Promocion extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('promociones')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'tipo',
        'cantidad',
        'pagar',
        'desde_mililitros',
        'hasta_mililitros',
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'estado'
    ];



    public function cervezas()
    {
        return $this->belongsToMany(Cerveza::class, 'promociones_productos')->withTimestamps();
    }
}
