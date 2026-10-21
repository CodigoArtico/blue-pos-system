<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class GastoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:crear-gastos', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-gastos', ['only' => ['update']]);
        $this->middleware('role_or_permission:eliminar-gastos', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
        // // Registrar los datos recibidos en el log
        // Log::info('Datos recibidos en la solicitud:', $request->all());

        $validatedData = $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'metodo_pagos_id' => 'required|exists:metodo_pagos,id',
            'estado' => 'nullable',
        ]);

        // Asignar automáticamente el ID del usuario autenticado
        $validatedData['user_id'] = auth()->id();

        // Agregar la fecha actual si no se proporciona
        $validatedData['fecha'] = $request->input('fecha', now());

        $gasto = Gasto::create($validatedData);

        return response()->json(['message' => 'Gasto creado con éxito', 'gasto' => $gasto], 201);
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
    // Método para actualizar un gasto
    public function update(Request $request, $id)
    {
        // Registrar los datos recibidos en el log
        // Log::info('Datos recibidos en la solicitud:', $request->all());
        // Validar los datos entrantes
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'metodo_pago' => 'required|exists:metodo_pagos,id',
            'monto' => 'required|numeric|min:0',
        ]);

        // Buscar el gasto por ID
        $gasto = Gasto::findOrFail($id);

        // Actualizar los datos del gasto
        $gasto->descripcion = $request->input('descripcion');
        $gasto->metodo_pagos_id = $request->input('metodo_pago');
        $gasto->monto = $request->input('monto');

        // Guardar cambios
        $gasto->save();

        return response()->json([
            'message' => 'Gasto actualizado con éxito',
            'gasto' => $gasto,
        ], 200);
    }

    // Método para eliminar (desactivar) un gasto
    public function destroy($id)
    {
        // Buscar el gasto por ID
        $gasto = Gasto::findOrFail($id);

        // Cambiar el estado a inactivo (0)
        $gasto->estado = 0;

        // Guardar cambios
        $gasto->save();

        return response()->json([
            'message' => 'Gasto eliminado con éxito',
        ], 200);
    }
}
