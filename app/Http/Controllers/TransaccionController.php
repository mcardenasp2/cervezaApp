<?php

namespace App\Http\Controllers;

use App\Models\Cerveza;
use App\Models\Pulsera;
use App\Models\Transaccion;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'codigo_uid' => 'required',
            'cerveza_id' => 'required|exists:cervezas,id',
            'mililitros_consumidos' => 'required|numeric|min:0',
        ]);

        $code = substr(trim($request->codigo_uid), 0, 8);

        $bracelet = Pulsera::where('codigo_uid',$code)->where('estado', 1)->first();

        $beer = Cerveza::findOrFail($request->cerveza_id);

        // Crear la transacción en la base de datos
        $transaction = Transaccion::create([
            'pulsera_id' => $bracelet->id,
            'cerveza_id' => $beer->id ,
            'codigo_uid' => $bracelet->codigo_uid,
            'mililitros_consumidos' => $request->mililitros_consumidos,
            'precio_por_mililitro' => $beer->precio_por_mililitro, // Si necesitas calcular el valor
            'total' => round($beer->precio_por_mililitro * $request->mililitros_consumidos , 2), // Si necesitas calcular el valor
            'estado' => 1, // Estado por defecto
        ]);


        return response()->json([
            'message' => 'Transacción registrada con éxito',
            'data' => $transaction,
        ], 201);
    }



}
