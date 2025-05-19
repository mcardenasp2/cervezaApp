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
        'hora_inicio',
        'hora_fin',
        'descripcion',
        'estado'
    ];

    protected $appends = ['horario_dias'];

    public function getDiasLabelAttribute()
    {
        return $this->dias->pluck('dia')->unique()->join(', ');
    }

    public function getHorarioDiasAttribute()
    {
        $result = $this->dias->first();

        if ($result) {
            return $result->hora_inicio . ' hasta ' . $result->hora_fin;
        }

        return 'Horario no asignado';  // Devuelve algo por defecto si no hay días

    }

    public function cervezas()
    {
        return $this->belongsToMany(Cerveza::class, 'promociones_productos')->withTimestamps();
    }

    public function dias()
    {
        return $this->hasMany(PromocionDia::class);
    }
}
