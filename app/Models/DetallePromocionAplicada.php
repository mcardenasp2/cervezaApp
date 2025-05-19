<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DetallePromocionAplicada extends Model
{
    use HasFactory;

    use LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll() // <-- todas las columnas
            ->useLogName('detalle_promocion_aplicada')
            ->logOnlyDirty() // solo si cambió algo
            ->dontSubmitEmptyLogs(); // no guardar si no hay cambios
    }

    protected $table = 'detalle_promocion_aplicada';



    protected $fillable = [
        'venta_id',
        'promocion_id',
        'cervezas_ids',
        'cantidad_mililitros',
        'cantidad_items_aplicados',
        'cantidad_gratis',
        'total_descuento',
        'descripcion_snapshot',
        'cantidad_promociones',
        'estado'
    ];

    public function venta()
    {
        return $this->belongsTo(VentasEncabezado::class, 'venta_id');
    }
}
