<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AsignacionPulsera extends Model
{

    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('asignacion_pulseras')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'asignacion_pulseras';

    protected $fillable = [
        'cliente_id',
        'usuario_id',
        'pulsera_id',
        'fecha_creacion',
        'fecha_inicio_asignacion',
        'fecha_fin_asignacion',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
    public function pulsera()
    {
        return $this->belongsTo(Pulsera::class);
    }
}
