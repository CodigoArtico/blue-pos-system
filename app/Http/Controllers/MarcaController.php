<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMarcaRequest;
use App\Models\Caracteristica;
use App\Models\Marca;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class MarcaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-marca', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-marca', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-marca', ['only' => ['update']]);
        $this->middleware('role_or_permission:eliminar-marca', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $marcas = Marca::with('caracteristica')->latest()->get();
        return view('marca.index', ['marcas' => $marcas]);
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
    public function store(StoreMarcaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear la característica
            $caracteristica = Caracteristica::create($request->validated());

            // Crear la marca asociada
            $caracteristica->marcas()->create([ // Cambiar 'marca()' por 'marcas()'
                'caracteristica_id' => $caracteristica->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Marca registrada con éxito.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la marca: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Marca $marca)
    {
        // Cargar la relación de caracteristica
        $marca->load('caracteristica');

        return response()->json([
            'success' => true,
            'data' => $marca,
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
    public function update(Request $request, $id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $caracteristica = $marca->caracteristica;

            $request->validate([
                'nombre' => 'required|max:60|unique:caracteristicas,nombre,' . $caracteristica->id . ',id',
                'descripcion' => 'nullable|max:255',
            ]);

            $caracteristica->update([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada con éxito.',
                'data' => $caracteristica,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la marca con ID ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la marca: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $marca = Marca::find($id);

    if (!$marca) {
        return response()->json([
            'success' => false,
            'message' => 'Marca no encontrada.',
        ], 404);
    }

    if ($marca->caracteristica->estado == 1) {
        Caracteristica::where('id', $marca->caracteristica->id)
            ->update(['estado' => 0]);

        return response()->json([
            'success' => true,
            'message' => 'Marca eliminada correctamente.',
            'action' => 'eliminar',
        ]);
    } else {
        Caracteristica::where('id', $marca->caracteristica->id)
            ->update(['estado' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Marca restaurada correctamente.',
            'action' => 'restaurar',
        ]);
    }
}
}
