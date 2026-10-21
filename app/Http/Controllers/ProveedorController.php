<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Documento;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class ProveedorController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-proveedor', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-proveedor', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-proveedor', ['only' => ['update', 'edit']]);
        $this->middleware('role_or_permission:eliminar-proveedor', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $proveedores = Proveedor::with('persona.documento')->orderBy('created_at', 'desc')->get();
        $documentos = Documento::all();
        return view('proveedor.index', compact('proveedores', 'documentos'));
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
    public function store(StoreProveedorRequest $request)
    {
        try {
            DB::beginTransaction();

            // Preparar los datos para crear una nueva Persona
            $personaData = [
                'documento_id' => $request->documento_id,
                'numero_documento' => $request->numero_documento,
                'direccion' => $request->direccion,
                'tipo_persona' => $request->tipo_persona,
                'estado' => 1, // Activo
                'razon_social' => $request->razon_social,
            ];

            // Crear la Persona en la base de datos
            $persona = Persona::create($personaData);

            // Crear el Proveedor asociado a la Persona
            $proveedor = Proveedor::create([
                'persona_id' => $persona->id,
            ]);

            DB::commit();

            // Retornar una respuesta JSON de éxito
            return response()->json([
                'success' => true,
                'message' => 'Proveedor registrado con éxito.',
                'data' => $proveedor
            ], 201); // 201 Created
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Retornar una respuesta JSON de error
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el proveedor: ' . $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedore)
    {
        try {
            $proveedore->load('persona.documento');
            return response()->json([
                'success' => true,
                'proveedore' => $proveedore
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del proveedor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProveedorRequest $request, Proveedor $proveedore)
    {
        try {
            DB::beginTransaction();

            // Actualizar los datos de la persona
            $personaData = [
                'documento_id' => $request->documento_id,
                'numero_documento' => $request->numero_documento,
                'direccion' => $request->direccion,
                'tipo_persona' => $request->tipo_persona,
                'razon_social' => $request->razon_social,
            ];

            $proveedore->persona->update($personaData);

            DB::commit();

            // Retornar una respuesta JSON de éxito
            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado con éxito.',
                'data' => $proveedore
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proveedor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedore)
    {
        try {
            $persona = $proveedore->persona;

            // Alternar el estado
            $persona->estado = $persona->estado == 1 ? 0 : 1;
            $persona->save();

            $message = $persona->estado == 1 ? 'Proveedor restaurado.' : 'Proveedor eliminado.';

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }
}
