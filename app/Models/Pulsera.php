<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pulsera extends Model
{
    use HasFactory;


    protected $table = 'pulseras';

    protected $fillable = ['codigo_serial', 'codigo_uid', 'estado'];

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }
}
