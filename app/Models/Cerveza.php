<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cerveza extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('cervezas')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'cervezas';

    protected $fillable = [
        'nombre',
        'precio_por_mililitro',
        'estado',
    ];

    public function promociones()
    {
        return $this->belongsToMany(Promocion::class, 'promociones_productos')->withTimestamps();
    }
}
