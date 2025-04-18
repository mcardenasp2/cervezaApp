<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VentasEncabezado extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('ventas_encabezados')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'ventas_encabezados';

    protected $fillable = [
        'pulsera_id',
        'user_id',
        'total',
        'transacciones_ids',
        'asignacion_pulsera_id',
        'cliente_id',
        'estado'
    ];

    // Relación con la pulsera
    public function pulsera()
    {
        return $this->belongsTo(Pulsera::class, 'pulsera_id');
    }

    // Relación con el usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con los detalles de venta
    public function detalles()
    {
        return $this->hasMany(VentasDetalle::class, 'cabecera_id')->where('estado', 1);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
