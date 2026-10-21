<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Caracteristica;
use App\Models\Categoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class CategoriaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-categoria', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-categoria', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-categoria', ['only' => ['update']]);
        $this->middleware('role_or_permission:eliminar-categoria', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $categorias = Categoria::with('caracteristica')->latest()->get();
        return view('category.index', ['categorias' => $categorias]);
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
    public function store(StoreCategoriaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear la característica
            $caracteristica = Caracteristica::create([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'destacado' => $request->input('destacado', 0), // Por defecto, será 0 si no está marcado
            ]);

            // Crear la categoría asociada
            $categoria = $caracteristica->categorias()->create([
                'caracteristica_id' => $caracteristica->id
            ]);

            DB::commit();
            // Log::info('Transacción completada correctamente');

            // Retornar respuesta JSON
            return response()->json([
                'success' => true,
                'message' => 'Categoría registrada con éxito.',
                'data' => $categoria // Incluye la información de la categoría
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar: ' . $e->getMessage());

            // Retornar error en JSON
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la categoría.',
                'error' => $e->getMessage() // Opcional
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        // Cargar la relación de caracteristica
        $categoria->load('caracteristica');

        return response()->json([
            'success' => true,
            'data' => $categoria,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
        // dd($categoria);
        // return view('categoria.edit', ['categoria' => $categoria]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Obtener la categoría que quieres actualizar
            $categoria = Categoria::findOrFail($id);

            // Obtener la 'caracteristica' relacionada
            $caracteristica = $categoria->caracteristica;

            // Validación de los campos de la caracteristica
            // Log::info('Datos recibidos: ', $request->all());
            $request->validate([
                'nombre' => 'required|max:60|unique:caracteristicas,nombre,' . $caracteristica->id . ',id',
                'descripcion' => 'nullable|max:255',
                'destacado' => 'nullable|boolean',  // Esto valida el checkbox, si está presente, será 1 (activo), si no, no se enviará.
            ]);

            // Actualizamos los datos de la 'caracteristica'
            $caracteristica->update([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'destacado' => $request->input('destacado'), // Por defecto, será 0 si no está marcado
            ]);

            // Responder con JSON
            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada con éxito.',
                'data' => $caracteristica,
            ], 200);
        } catch (\Exception $e) {
            // Registrar el error en el log
            Log::error('Error al actualizar la categoría con ID ' . $id . ': ' . $e->getMessage());

            // Responder con error en JSON
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada.',
            ], 404);
        }

        try {
            if ($categoria->caracteristica->estado == 1) {
                Caracteristica::where('id', $categoria->caracteristica->id)
                    ->update(['estado' => 0]);

                return response()->json([
                    'success' => true,
                    'message' => 'Categoría eliminada correctamente.',
                    'action' => 'eliminar',
                ], 200);
            } else {
                Caracteristica::where('id', $categoria->caracteristica->id)
                    ->update(['estado' => 1]);

                return response()->json([
                    'success' => true,
                    'message' => 'Categoría restaurada correctamente.',
                    'action' => 'restaurar',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage(),
            ], 500);
        }
    }
}
