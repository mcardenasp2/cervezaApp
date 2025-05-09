<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaccion extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('transacciones')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'transacciones';

    protected $fillable = [
        'pulsera_id',
        'codigo_uid',
        'cerveza_id',
        'mililitros_consumidos',
        'precio_por_mililitro',
        'total',
        'pagado',
        'estado',
    ];

    public function pulsera()
    {
        return $this->belongsTo(Pulsera::class, 'pulsera_id');
    }

    public function cerveza()
    {
        return $this->belongsTo(Cerveza::class);
    }
}
