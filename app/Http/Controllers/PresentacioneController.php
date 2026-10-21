<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresentacioneRequest;
use App\Models\Caracteristica;
use App\Models\Presentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Routing\Controller as BaseController;

class PresentacioneController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-presentacion', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-presentacion', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-presentacion', ['only' => ['update']]);
        $this->middleware('role_or_permission:eliminar-presentacion', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $presentaciones = Presentacion::with('caracteristica')->latest()->get();
        return view('presentacion.index', ['presentaciones' => $presentaciones]);
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
    public function store(StorePresentacioneRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear la característica
            $caracteristica = Caracteristica::create($request->validated());

            // Crear la presentación asociada
            $caracteristica->presentaciones()->create([
                'caracteristica_id' => $caracteristica->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Presentación registrada con éxito.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la presentación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Presentacion $presentacione)
    {
        // Cargar la relación de caracteristica
        $presentacione->load('caracteristica');

        return response()->json([
            'success' => true,
            'data' => $presentacione,
        ]);
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
    public function update(Request $request, Presentacion $presentacione)
    {
        // Validar los datos de entrada
        $request->validate([
            'nombre' => 'required|max:60|unique:caracteristicas,nombre,' . $presentacione->caracteristica->id,
            'descripcion' => 'nullable|max:255',
        ]);

        // Actualizar la característica asociada
        Caracteristica::where('id', $presentacione->caracteristica->id)
            ->update($request->only('nombre', 'descripcion'));

        return response()->json([
            'success' => true,
            'message' => 'Presentación editada con éxito.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $presentacione = Presentacion::find($id);
        if (!$presentacione) {
            return response()->json(['success' => false, 'message' => 'Presentación no encontrada.'], 404);
        }

        $estado = $presentacione->caracteristica->estado == 1 ? 0 : 1;
        $presentacione->caracteristica->update(['estado' => $estado]);

        return response()->json(['success' => true, 'message' => $estado == 1 ? 'Presentación restaurada.' : 'Presentación eliminada.']);
    }
}
