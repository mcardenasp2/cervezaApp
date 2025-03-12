<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasEncabezado extends Model
{
    use HasFactory;

    protected $table = 'ventas_encabezados';

    protected $fillable = [
        'pulsera_id',
        'user_id',
        'total',
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
}
