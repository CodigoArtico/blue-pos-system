<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.apis.token', env('APIS_TOKEN'));
    }

    /**
     * Consulta DNI - Nueva API
     */
    public function getDni(Request $request)
    {
        $request->validate([
            'dato' => 'required|digits:8',
        ], [
            'dato.required' => 'El DNI es requerido.',
            'dato.digits' => 'El DNI debe contener exactamente 8 dígitos.',
        ]);

        $dni = $request->input('dato');

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get("https://api-codart.cgrt.org/api/v1/consultas/reniec/dni/{$dni}");

            if ($response->failed()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Error al consultar el DNI.',
                ], 500);
            }

            $data = $response->json();

            if (!$data['success']) {
                return response()->json([
                    'error' => true,
                    'message' => 'No se pudo obtener información del DNI.',
                ], 500);
            }

            $result = $data['result'];

            // Mantener misma estructura de respuesta que el frontend espera
            return response()->json([
                'numeroDocumento' => $result['document_number'] ?? '',
                'nombre' => ($result['first_name'] ?? '') . ' ' .
                    ($result['first_last_name'] ?? '') . ' ' .
                    ($result['second_last_name'] ?? ''),
                'tipoDocumento' => $result['tipo_documento'] ?? '1',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error al consultar el DNI.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Consulta RUC - Nueva API
     */
    public function getRuc(Request $request)
    {
        $request->validate([
            'dato' => 'required|digits:11',
        ], [
            'dato.required' => 'El RUC es requerido.',
            'dato.digits' => 'El RUC debe contener exactamente 11 dígitos.',
        ]);

        $ruc = $request->input('dato');

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get("https://api-codart.cgrt.org/api/v1/consultas/sunat/ruc/{$ruc}");

            if ($response->failed()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Error al consultar el RUC.',
                ], 500);
            }

            $data = $response->json();

            if (!$data['success']) {
                return response()->json([
                    'error' => true,
                    'message' => 'No se pudo obtener información del RUC.',
                ], 500);
            }

            $result = $data['result'];

            // Mantener misma estructura de respuesta que el frontend espera
            return response()->json([
                'numeroDocumento' => $result['numero_documento'] ?? '',
                'razonSocial' => $result['razon_social'] ?? '',
                'condicion' => $result['condicion'] ?? '',
                'estado' => $result['estado'] ?? '',
                'direccion' => $result['direccion'] ?? '',
                'departamento' => $result['departamento'] ?? '',
                'distrito' => $result['distrito'] ?? '',
                'provincia' => $result['provincia'] ?? '',
                'tipoDocumento' => $result['tipo_documento'] ?? '6',
                'ubigeo' => $result['ubigeo'] ?? '',
                'viaNombre' => $result['via_nombre'] ?? '',
                'viaTipo' => $result['via_tipo'] ?? '',
                'zonaTipo' => $result['zona_tipo'] ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error al consultar el RUC.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function guardarCliente(Request $request)
    {
        // Registrar los datos recibidos
        // Log::info('Intentando guardar un nuevo cliente.', [
        //     'input' => $request->all()
        // ]);

        // Transformar datos para manejar arreglos vacíos
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (is_array($value) && empty($value)) {
                $data[$key] = '-';
            } elseif ($value === null || $value === '') {
                $data[$key] = '-';
            } elseif (is_array($value) && isset($value[0]) && is_array($value[0]) && empty($value[0])) {
                $data[$key] = '-';
            }
        }

        // Crear un nuevo Request con los datos transformados
        $request->merge($data);

        // Definir las reglas de validación
        $rules = [
            'tipo_persona' => 'required|in:natural,juridica',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'documento_id' => 'required|integer|exists:documentos,id',
            'numero_documento' => 'required|string|max:20',
            // extras añadidos
            'condicionRUC' => 'nullable|string',
            'estadoRUC' => 'nullable|string',
            'departamento' => 'nullable|string',
            'distrito' => 'nullable|string',
            'provincia' => 'nullable|string',
            'tipoDocumento_Sunat' => 'nullable|string',
            'ubigeo' => 'nullable|string',
            'viaNombre' => 'nullable|string',
            'viaTipo' => 'nullable|string',
            'zonaTipo' => 'nullable|string',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'tipo_persona.required' => 'El tipo de persona es requerido.',
            'tipo_persona.in' => 'El tipo de persona debe ser natural o juridica.',
            'razon_social.required' => 'La razón social es requerida.',
            'documento_id.required' => 'El documento es requerido.',
            'documento_id.exists' => 'El documento seleccionado no es válido.',
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.max' => 'El número de documento no debe exceder los 20 caracteres.',
        ];

        // Crear el validador
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir una validación personalizada después de las reglas básicas
        $validator->after(function ($validator) use ($request) {
            $exists = Persona::where('numero_documento', $request->numero_documento)
                ->whereHas('cliente')
                ->exists();
            if ($exists) {
                $validator->errors()->add('numero_documento', 'El número de documento ya está registrado como cliente.');
            }
        });

        // Verificar si la validación falla
        if ($validator->fails()) {
            Log::warning('Validación de datos fallida.', [
                'errors' => $validator->errors()
            ]);

            // Verificar si el error está relacionado con 'numero_documento'
            if ($validator->errors()->has('numero_documento')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un cliente con ese número de documento.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Respuesta para otros errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Hay errores en la validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Iniciar una transacción
            DB::beginTransaction();

            // Crear una nueva instancia de Persona
            $persona = Persona::create([
                'tipo_persona' => $request->tipo_persona,
                'razon_social' => $request->razon_social,
                'direccion' => $request->direccion,
                'documento_id' => $request->documento_id,
                'numero_documento' => $request->numero_documento,
                'condicion' => $request->condicionRUC,
                'estado_sunat' => $request->estadoRUC,
                'departamento' => $request->departamento,
                'distrito' => $request->distrito,
                'provincia' => $request->provincia,
                'tipo_documento' => $request->tipoDocumento_Sunat,
                'ubigeo' => $request->ubigeo,
                'via_nombre' => $request->viaNombre,
                'via_tipo' => $request->viaTipo,
                'zona_tipo' => $request->zonaTipo,
            ]);

            // Crear una nueva instancia de Cliente asociada a la Persona
            $cliente = Cliente::create([
                'persona_id' => $persona->id,
                // Agrega otros campos necesarios para Cliente si los hay
            ]);

            // Confirmar la transacción
            DB::commit();

            // Cargar las relaciones 'persona' y 'documento'
            $cliente->load('persona.documento');

            return response()->json([
                'success' => true,
                'message' => 'Cliente guardado exitosamente.',
                'data' => $cliente,
            ], 201);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Registrar el error en el log
            Log::error('Error al guardar el cliente.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Responder con detalles adicionales solo en desarrollo
            $response = [
                'success' => false,
                'message' => 'Ocurrió un error al guardar el cliente.'
            ];

            if (config('app.debug')) {
                $response['error'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
            }

            return response()->json($response, 500);
        }
    }

    public function guardarProveedor(Request $request)
    {
        // Registrar los datos recibidos
        // Log::info('Intentando guardar un nuevo PROVEEDOR.', [
        //     'input' => $request->all()
        // ]);

        // Transformar datos para manejar arreglos vacíos
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (is_array($value) && empty($value)) {
                $data[$key] = '-';
            } elseif ($value === null || $value === '') {
                $data[$key] = '-';
            } elseif (is_array($value) && isset($value[0]) && is_array($value[0]) && empty($value[0])) {
                $data[$key] = '-';
            }
        }

        // Crear un nuevo Request con los datos transformados
        $request->merge($data);

        // Definir las reglas de validación
        $rules = [
            'tipo_persona' => 'required|in:natural,juridica',
            'razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'documento_id' => 'required|integer|exists:documentos,id',
            'numero_documento' => 'required|string|max:20',
            // extras añadidos
            'condicionRUC' => 'nullable|string',
            'estadoRUC' => 'nullable|string',
            'departamento' => 'nullable|string',
            'distrito' => 'nullable|string',
            'provincia' => 'nullable|string',
            'tipoDocumento_Sunat' => 'nullable|string',
            'ubigeo' => 'nullable|string',
            'viaNombre' => 'nullable|string',
            'viaTipo' => 'nullable|string',
            'zonaTipo' => 'nullable|string',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'tipo_persona.required' => 'El tipo de persona es requerido.',
            'tipo_persona.in' => 'El tipo de persona debe ser natural o jurídica.',
            'razon_social.required' => 'La razón social es requerida.',
            'documento_id.required' => 'El documento es requerido.',
            'documento_id.exists' => 'El documento seleccionado no es válido.',
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.max' => 'El número de documento no debe exceder los 20 caracteres.',
        ];

        // Crear el validador
        $validator = Validator::make($request->all(), $rules, $messages);

        // Añadir una validación personalizada después de las reglas básicas
        $validator->after(function ($validator) use ($request) {
            $exists = Persona::where('numero_documento', $request->numero_documento)
                ->whereHas('proveedor')
                ->exists();
            if ($exists) {
                $validator->errors()->add('numero_documento', 'El número de documento ya está registrado como proveedor.');
            }
        });

        // Verificar si la validación falla
        if ($validator->fails()) {
            Log::warning('Validación de datos fallida.', [
                'errors' => $validator->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Hay errores en la validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Iniciar una transacción
            DB::beginTransaction();

            // Crear una nueva instancia de Persona
            $persona = Persona::create([
                'tipo_persona' => $request->tipo_persona,
                'razon_social' => $request->razon_social,
                'direccion' => $request->direccion,
                'documento_id' => $request->documento_id,
                'numero_documento' => $request->numero_documento,
                'condicion' => $request->condicionRUC,
                'estado_sunat' => $request->estadoRUC,
                'departamento' => $request->departamento,
                'distrito' => $request->distrito,
                'provincia' => $request->provincia,
                'tipo_documento' => $request->tipoDocumento_Sunat,
                'ubigeo' => $request->ubigeo,
                'via_nombre' => $request->viaNombre,
                'via_tipo' => $request->viaTipo,
                'zona_tipo' => $request->zonaTipo,
            ]);

            // Crear una nueva instancia de proveedor asociada a la Persona
            $proveedor = Proveedor::create([
                'persona_id' => $persona->id,
                // Agrega otros campos necesarios para proveedor si los hay
            ]);

            // Confirmar la transacción
            DB::commit();

            // Cargar las relaciones 'persona' y 'documento'
            $proveedor->load('persona.documento');

            return response()->json([
                'success' => true,
                'message' => 'Proveedor guardado exitosamente.',
                'data' => $proveedor,
            ], 201);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Registrar el error en el log
            Log::error('Error al guardar el proveedor.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Responder con detalles adicionales solo en desarrollo
            $response = [
                'success' => false,
                'message' => 'Ocurrió un error al guardar el proveedor.'
            ];

            if (config('app.debug')) {
                $response['error'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
            }

            return response()->json($response, 500);
        }
    }
}
