<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;
    protected $table = 'transacciones';

    protected $fillable = [
        'pulsera_id',
        'codigo_uid',
        'cerveza_id',
        'mililitros_consumidos',
        'valor',
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
