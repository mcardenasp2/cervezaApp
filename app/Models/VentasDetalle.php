<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDetalle extends Model
{
    use HasFactory;

    protected $table = 'ventas_detalles';

    protected $fillable = [
        'cabecera_id',
        'cerveza_id',
        'mililitros_consumidos',
        'precio_por_mililitro',
        'total',
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
