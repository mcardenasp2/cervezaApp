<?php

namespace App\Http\Controllers;

use App\Models\Cerveza;
use App\Models\Transaccion;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'pulsera_id' => 'required|exists:pulseras,id',
            'cerveza_id' => 'required|exists:cervezas,id',
            'mililitros_consumidos' => 'required|numeric|min:0',
        ]);


        $beer = Cerveza::findOrFail($request->cerveza_id);

        // Crear la transacción en la base de datos
        $transaction = Transaccion::create([
            'pulsera_id' => $request->pulsera_id,
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
