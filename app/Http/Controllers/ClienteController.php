<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class ClienteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-cliente', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-cliente', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-cliente', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:eliminar-cliente', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $clientes = Cliente::with('persona.documento')->orderBy('created_at', 'desc')->get();
        $documentos = Documento::all();
        return view('client.index', compact('documentos', 'clientes'));
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
    public function store(StoreClienteRequest $request)
    {
        try {
            DB::beginTransaction();

            // Preparar los datos para crear una nueva Persona
            $personaData = [
                'documento_id' => $request->documento_id,
                'numero_documento' => $request->numero_documento,
                'direccion' => $request->direccion,
                'tipo_persona' => $request->tipo_persona,
                'estado' => 1, // Asumiendo que 1 es activo
            ];

            // Asignar 'razon_social' directamente
            $personaData['razon_social'] = $request->razon_social;

            // Crear la Persona en la base de datos
            $persona = Persona::create($personaData);

            // Crear el Cliente asociado a la Persona
            $cliente = Cliente::create([
                'persona_id' => $persona->id,
            ]);

            DB::commit();

            // Retornar una respuesta JSON de éxito
            return response()->json([
                'success' => true,
                'message' => 'Cliente registrado con éxito.',
                'data' => $cliente
            ], 201); // 201 Created
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Retornar una respuesta JSON de error
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el cliente: ' . $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
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
    public function edit(Cliente $cliente)
    {
        try {
            $cliente->load('persona.documento');
            return response()->json([
                'success' => true,
                'cliente' => $cliente
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del cliente: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClienteRequest $request, Cliente $cliente)
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

            $cliente->persona->update($personaData);

            DB::commit();

            // Retornar una respuesta JSON de éxito
            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado con éxito.',
                'data' => $cliente
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        try {
            $persona = $cliente->persona;

            // Alternar el estado
            $persona->estado = $persona->estado == 1 ? 0 : 1;
            $persona->save();

            $message = $persona->estado == 1 ? 'Cliente restaurado.' : 'Cliente eliminado.';

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage(),
            ], 500);
        }
    }
}
