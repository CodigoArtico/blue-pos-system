<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Empresa;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use App\Rules\ValidarExtensionCertificado;
use Intervention\Image\ImageManagerStatic as Image;


use Illuminate\Routing\Controller as BaseController;

class EmpresaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-empresa', ['only' => ['index']]);
        $this->middleware('role_or_permission:configuracion-datos-empresa', ['only' => ['store', 'update']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtén el único registro de empresa
        $empresa = Empresa::first();

        // Verifica si hay al menos dos ventas
        $venta = Venta::skip(1)->first(); // Intentamos obtener el segundo registro

        // Si no hay segundo registro, obtenemos el primero
        if (!$venta) {
            $venta = Venta::first(); // Si no existe el segundo, obtenemos el primero
        }

        // Asigna $venta a $item
        $item = $venta;

        // Retorna la vista con los datos de la empresa y $item
        return view('empresa.index', compact('empresa', 'item'));
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
    //AQUI SOLO SUBE/ACTUALIZA DETALLES DE EMPRESA NOMBRE, DIRECCION Y NUMERO DE DOCUMENTO
    public function store(Request $request)
    {
        // Registrar los datos recibidos
        // Log::info('Intentando editar el primer registro de empresa.', [
        //     'input' => $request->all()
        // ]);

        // Obtener todos los datos del request
        $data = $request->all();

        // Reemplazar los valores vacíos con "-"
        foreach ($data as $key => $value) {
            if (is_array($value) && empty($value)) {
                $data[$key] = '-';
            } elseif ($value === null || $value === '') {
                $data[$key] = '-';
            } elseif (is_array($value) && isset($value[0]) && is_array($value[0]) && empty($value[0])) {
                $data[$key] = '-';
            }
        }

        // Opcional: Fusionar los datos modificados de vuelta en el request
        $request->merge($data);

        // Definir las reglas de validación
        $rules = [
            'razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'numero_documento' => 'required|string|min:8|max:11',

            'ubigeo' => 'nullable|string|max:80',
            'departamento' => 'nullable|string|max:80',
            'provincia' => 'nullable|string|max:80',
            'distrito' => 'nullable|string|max:80',
        ];

        // Definir los mensajes de error personalizados
        $messages = [
            'razon_social.required' => 'La razón social es requerida.',
            'numero_documento.required' => 'El número de documento es requerido.',
            'numero_documento.max' => 'El número de documento no debe exceder los 11 caracteres.',
        ];

        // Crear el validador usando los datos modificados
        $validator = Validator::make($data, $rules, $messages);

        // Verificar si la validación falla
        if ($validator->fails()) {
            Log::warning('Validación de datos fallida.', [
                'errors' => $validator->errors()
            ]);

            // Verificar si el error está relacionado con 'numero_documento'
            if ($validator->errors()->has('numero_documento')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Revisa el documento.',
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

            // Obtener el primer registro de Empresa
            $empresa = Empresa::first();

            if (!$empresa) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún registro de empresa.',
                ], 404);
            }

            // Actualizar solo los campos especificados usando $data
            $empresa->razon_social = $data['razon_social'];
            $empresa->direccion_empresa = $data['direccion'];
            $empresa->ruc_dni = $data['numero_documento'] ?? '00000000';
            $empresa->ubigeo = $data['ubigeo'];
            $empresa->departamento = $data['departamento'];
            $empresa->provincia = $data['provincia'];
            $empresa->distrito = $data['distrito'];
            $empresa->save();

            // Actualizar ruc del certificado para usarlo en emisiones
            // Obtén el primer registro del modelo Certificado
            $certificado = Certificado::first();

            // Verifica si se encontró un registro
            if ($certificado) {
                // Actualiza el campo ruc
                $certificado->ruc = $data['numero_documento'];
                // Guarda el registro
                $certificado->save();
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registro actualizado correctamente.',
                'data' => $empresa, // Opcional: Retorna la empresa actualizada
            ], 200);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollBack();

            // Registrar el error en el log
            Log::error('Error al actualizar la empresa.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Responder con detalles adicionales solo en desarrollo
            $response = [
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la empresa.'
            ];

            if (config('app.debug')) {
                $response['error'] = $e->getMessage();
                $response['trace'] = $e->getTrace();
            }

            return response()->json($response, 500);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        // Iniciar la transacción
        DB::beginTransaction();

        try {
            // Registrar los datos recibidos para depuración
            // Log::info('Intentando actualizar los datos de la empresa.', [
            //     'input' => $request->all()
            // ]);

            $empresa = Empresa::findOrFail($id);

            // Definir las reglas de validación pata tabla EMPRESA y CERTIFICADO
            $rules = [
                'razon_social_comprobante' => 'nullable|string|max:255',
                'telefono_comprobante' => 'nullable|string|max:20',
                'direccion_comprobante' => 'nullable|string|max:255',
                'subtitulo_principal_comprobante' => 'nullable|string|max:255',
                'descripcion_aviso_comprobante' => 'nullable|string',
                'despedida_comprobante' => 'nullable|string|max:255',
                'img_account' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'certificado_archivo' => [
                    'nullable',
                    'file',
                    'max:2048',
                    function ($attribute, $value, $fail) {
                        $extension = strtolower($value->getClientOriginalExtension());
                        if (!in_array($extension, ['pfx', 'pem'])) {
                            $fail('El campo ' . $attribute . ' debe ser un archivo de tipo: pem, pfx.');
                        }
                    },
                ],
                'certificado_password' => 'nullable|string|max:255',
                'ruc' => 'nullable|max:11',
                'usuario_sunat' => 'nullable|string|max:255',
                'password_sunat' => 'nullable|string|max:255',
                'client_id_sire' => 'nullable|string|max:255',
                'client_secret_sire' => 'nullable|string|max:255',
                'estado' => 'nullable',
            ];

            // Validar los datos
            $validator = Validator::make($request->all(), $rules);

            // Validación condicional para la contraseña del PFX usando $request
            $validator->sometimes('certificado_password', 'required', function ($input) use ($request) {
                if ($request->hasFile('certificado_archivo')) {
                    $file = $request->file('certificado_archivo');

                    // Depuración: Verificar el tipo y la extensión del archivo
                    // Log::info('Tipo de archivo certificado_archivo:', ['type' => get_class($file)]);
                    // Log::info('Extensión del archivo certificado_archivo:', ['extension' => $file->getClientOriginalExtension()]);
                    // Log::info('MIME type del archivo certificado_archivo: ' . $file->getMimeType());

                    return strtolower($file->getClientOriginalExtension()) === 'pfx';
                }
                return false;
            });

            if ($validator->fails()) {
                // Lanzar una excepción para que la transacción se revierta
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            // Actualizar los campos de Detalles del Comprobante si están presentes en la solicitud
            $empresa->fill($request->only([
                'razon_social_comprobante',
                'telefono_comprobante',
                'direccion_comprobante',
                'subtitulo_principal_comprobante',
                'descripcion_aviso_comprobante',
                'despedida_comprobante',
            ]));

            // Manejar la imagen del comprobante
            if ($request->hasFile('img_account')) {
                if ($empresa->img_account && Storage::disk('public')->exists('img_account_empresa/' . $empresa->img_account)) {
                    Storage::disk('public')->delete('img_account_empresa/' . $empresa->img_account);
                }

                $image = $request->file('img_account');
                $imageName = uniqid() . '.webp';
                $imagePath = 'img_account_empresa/' . $imageName;

                // Convertir la imagen a .webp
                $webpImage = Image::make($image)
                    ->encode('webp', 90); // Ajusta la calidad si lo deseas

                // Guardar la imagen en el almacenamiento público
                Storage::disk('public')->put($imagePath, (string) $webpImage);

                // Guardar solo el nombre de la imagen en la base de datos
                $empresa->img_account = $imageName;
            }

            $empresa->save();

            // Manejar la actualización del Certificado
            if ($empresa->certificado) {
                $certificado = $empresa->certificado;
            } else {
                // Si no existe, crearlo
                $certificado = new Certificado();
                $certificado->empresa_id = $empresa->id;
            }

            // Obtén el primer registro del modelo empresa para actualizar
            $empresa_ruc_dni = Empresa::first();
            // Verifica si se encontró un registro
            if ($empresa_ruc_dni) {
                // Actualiza el campo ruc
                $empresa_ruc_dni->ruc_dni = $request->ruc ?? '00000000';
                // Guarda el registro
                $empresa_ruc_dni->save();
            }
            // Actualizar los campos del Certificado si están presentes
            $certificado->ruc = $request->input('ruc', $certificado->ruc);
            $certificado->usuario_sunat = $request->input('usuario_sunat', $certificado->usuario_sunat);
            $certificado->password_sunat = $request->input('password_sunat', $certificado->password_sunat);
            $certificado->client_id_sire = $request->input('client_id_sire', $certificado->client_id_sire);
            $certificado->client_secret_sire = $request->input('client_secret_sire', $certificado->client_secret_sire);
            if ($request->has('estado')) {
                $certificado->estado = $request->input('estado');
            }

            // Manejar el archivo del certificado
            if ($request->hasFile('certificado_archivo')) {
                $file = $request->file('certificado_archivo');
                $password = $request->input('certificado_password', null);
                $certificado->manejarArchivoCertificado($file, $password);
            }

            $certificado->save();

            // Confirmar la transacción
            DB::commit();

            // Devolver una respuesta JSON exitosa
            return response()->json([
                'success' => true,
                'message' => 'Actualizado Correctamente.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Revertir la transacción
            DB::rollBack();

            // Devolver errores de validación
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Revertir la transacción
            DB::rollBack();

            // Registrar el error
            Log::error('Error al actualizar la empresa: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            // Devolver una respuesta de error detallada en JSON
            return response()->json([
                'success' => false,
                'errors' => ['general' => [$e->getMessage()]] // Retorna el mensaje de error exacto
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
