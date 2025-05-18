<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VentasDetalle extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('ventas_detalles')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'ventas_detalles';

    protected $fillable = [
        'cabecera_id',
        'cerveza_id',
        'mililitros_consumidos',
        'precio_por_mililitro',
        'total',
        'aplica_promocion',
        'producto_promocionado',
        'promocion_id',
        'fecha_transaccion',
        'estado'
    ];

    // Relación con la cabecera de venta
    public function encabezado()
    {
        return $this->belongsTo(VentasEncabezado::class, 'cabecera_id');
    }

    // Relación con la cerveza
    public function cerveza()
    {
        return $this->belongsTo(Cerveza::class, 'cerveza_id');
    }
}
