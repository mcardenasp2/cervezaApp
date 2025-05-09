<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

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

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];


    public function cervezas()
    {
        return $this->belongsToMany(Cerveza::class, 'promociones_productos')->withTimestamps();
    }
}
