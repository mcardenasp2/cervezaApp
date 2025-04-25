<?php

namespace App\Http\Controllers;

use App\Models\AsignacionPulsera;
use App\Models\Pulsera;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PulserasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'codigo_serial' => [
                'required',
                Rule::unique('pulseras', 'codigo_serial')->where(function ($query) {
                    return $query->where('estado', 1);
                }),
            ],
            'codigo_uid' => [
                'required',
                Rule::unique('pulseras', 'codigo_uid')->where(function ($query) {
                    return $query->where('estado', 1);
                }),
            ],
        ]);

        // Crear la pulsera en la base de datos
        $bracelet = Pulsera::create([
            'codigo_serial' => $request->codigo_serial,
            'codigo_uid' => $request->codigo_uid,
            'estado' => 1, // Estado por defecto
        ]);

        return response()->json([
            'message' => 'Pulsera registrada con éxito',
            'data' => $bracelet,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function checkAssignation(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'codigo_uid' => 'required',
        ]);

        $code = substr(trim($request->codigo_uid), 0, 8);
        // Buscar la pulsera por el código UID
        $bracelet = Pulsera::where([
            'codigo_uid' => $code,
            'estado' => 1,
        ])->first();

        // Verificar si la pulsera existe
        if (!$bracelet) {
            return response()->json([
                'message' => 'Pulsera no encontrada o inactiva',
                'data' => false,
            ], 403);
        }

        $braceletAssignment= AsignacionPulsera::where([
            'pulsera_id' => $bracelet->id,
            'estado' => 1,
        ])->first();

        // Verificar si la pulsera está asignada
        if (!$braceletAssignment) {
            return response()->json([
                'message' => 'Pulsera no asignada',
                'data' => false,
            ], 403);
        }
        return response()->json([
            'message' => 'Pulsera asignada con éxito',
            'data' => true,
        ], 200);


    }

}
