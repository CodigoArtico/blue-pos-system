<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\BajaComprobante;
use App\Models\Cdr;
use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\CorrelativoUsado;
use App\Models\EmisionComprobante;
use App\Models\Empresa;
use App\Models\Mesa;
use App\Models\MetodoPago;
use App\Models\Orden;
use App\Models\Producto;
use App\Models\Producto_Libre;
use App\Models\Venta;
use Exception;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use NumberToWords\NumberToWords;
use ZipArchive;
use Greenter\Model\Company\Address;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

use Illuminate\Routing\Controller as BaseController;

class VentaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-ventas', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-venta', ['only' => ['store', 'create']]);
        $this->middleware('role_or_permission:eliminar-venta', ['only' => ['destroy']]);
    }

    // Definir la función dentro de la clase
    public function configurarGreenter($certificado)
    {
        $see = new See();

        // Configurar servicio (puedes cambiar a FE_PRODUCCION para producción)
        $see->setService(SunatEndpoints::FE_BETA); // Cambia a FE_PRODUCCION para producción

        // Configurar el certificado
        $see->setCertificate(file_get_contents(storage_path('app/public/certificados/' . $certificado->certificado)));

        // Configurar las credenciales de SUNAT
        $see->setClaveSOL($certificado->ruc, $certificado->usuario_sunat, $certificado->password_sunat);

        return $see;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfDay()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        // Asegurar que $endDate sea al menos igual a $startDate
        if ($endDate < $startDate) {
            $endDate = $startDate;
        }

        $ventas = Venta::with([
            'comprobante',
            'cliente.persona',
            'user',
            'emisionComprobante.bajaComprobante'
        ])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        foreach ($ventas as $venta) {
            $venta->mensaje_cdr = $venta->emisionComprobante?->cdrs->first()?->mensaje ?? '';
            $venta->contenido_xml = $venta->emisionComprobante?->cdrs->first()?->contenido_xml ?? '';
        }
        // Opcional: ver los valores en el log
        // Log::info($ventas->pluck('mensaje_cdr'));

        // Calcular el total de ventas en el rango de fechas
        $totalVentas = Venta::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('estado', 1)  // Filtra por estado igual a 1
            ->sum('total');
        $totalVentasAnulados = Venta::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('estado', 0)  // Filtra por estado igual a 1
            ->sum('total');

        // Obtener los productos libres de las ventas en el rango de fechas
        $productos_libres = [];
        foreach ($ventas as $venta) {
            $productos_libres[$venta->id] = Producto_Libre::where('venta_id', $venta->id)->get();
        }

        return view('venta.index', compact('ventas', 'startDate', 'endDate', 'totalVentas', 'totalVentasAnulados', 'productos_libres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Recupera el código de orden o mesa enviado en la solicitud
        $codigoOrden = $request->get('orden');
        $codigoMesa = $request->get('mesa');

        $productosPrecargados = [];

        if ($codigoOrden) {
            // Buscar la orden por su código
            $orden = Orden::with('productos')->where('codigo_orden', $codigoOrden)->first();

            if (!$orden) {
                return redirect()->back()->with('error', 'La orden no existe o no es válida.');
            }

            // Cargar los productos relacionados con la orden
            $productosPrecargados = $orden->productos->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'stock' => $producto->stock,
                    'precio_venta' => $producto->precioVentaReciente() ?? 0,
                    'cantidad' => $producto->pivot->cantidad,
                    'precio' => $producto->pivot->precio,
                ];
            });
        } elseif ($codigoMesa) {
            // Buscar la mesa por su código
            $mesa = Mesa::with('productos')->where('codigo_mesa', $codigoMesa)->first();

            if (!$mesa) {
                return redirect()->back()->with('error', 'La mesa no existe o no es válida.');
            }

            // Cargar los productos relacionados con la mesa
            $productosPrecargados = $mesa->productos->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'stock' => $producto->stock,
                    'precio_venta' => $producto->precioVentaReciente() ?? 0,
                    'cantidad' => $producto->pivot->cantidad,
                    'precio' => $producto->pivot->precio,
                ];
            });
        }

        // Cargar métodos de pago
        $metodosPago = MetodoPago::all();

        // Obtener la venta más reciente
        $ultimaVenta = Venta::latest()->first();

        // Configurar la sesión con la última venta (si existe)
        if ($ultimaVenta) {
            session(['venta' => $ultimaVenta]);
        }
        // Cargar métodos de pago
        $metodosPago = MetodoPago::all();
        //pasar dato para mostar el ultimo digito +1 de la tabla ventas (numero_comprobante)
        // Obtener el último registro de ventas
        $ultimaVenta = Venta::orderBy('id', 'desc')->first();

        // Obtener el último número de comprobante, manejando el caso en el que no haya ventas
        $ultimoNumeroComprobante = $ultimaVenta->numero_comprobante ?? 0;

        // Calcular el próximo número de comprobante sumándole 1 al último número
        $numeroComprobante = $ultimoNumeroComprobante + 1;

        // Registro de depuración
        // Log::info('Último número de comprobante: ' . $ultimoNumeroComprobante);
        // Log::info('Próximo número de comprobante: ' . $numeroComprobante);

        $subquery = DB::table('compra_productos as cpr')
            ->join('compras as c', 'c.id', '=', 'cpr.compra_id')  // Aseguramos que estamos uniendo con la tabla de compras
            ->where('c.estado', 1)  // Solo compras activas
            ->select('cpr.producto_id', DB::raw('MAX(cpr.created_at) as max_created_at'))
            ->groupBy('cpr.producto_id');  // Obtenemos la fecha más reciente por producto

        $productos = Producto::joinSub($subquery, 'latest_purchase', function ($join) {
            $join->on('productos.id', '=', 'latest_purchase.producto_id');
        })
            ->join('compra_productos as cpr', function ($join) {
                $join->on('cpr.producto_id', '=', 'productos.id')
                    ->on('cpr.created_at', '=', 'latest_purchase.max_created_at');  // Solo la compra más reciente
            })
            ->join('compras as c', 'c.id', '=', 'cpr.compra_id')  // Unimos con la tabla de compras para filtrar por estado
            ->where('productos.estado', 1)  // Solo productos activos
            // ->where('productos.stock', '>=', 0)  // Productos con stock disponible
            ->where('c.estado', 1)  // Solo compras activas
            ->select('productos.codigo', 'productos.nombre', 'productos.id', 'productos.stock', 'cpr.precio_venta')
            ->get();


        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();
        // Obtener la primera empresa
        $empresa = Empresa::first();
        $certificado = Certificado::first();
        // Verificar si la empresa existe
        if (!$empresa) {
            return redirect()->back()->with('error', 'No hay Empresa.');
        }
        // Verificar si la empresa tiene un certificado asociado
        if (!$empresa->certificado) {
            return redirect()->back()->with('error', 'La empresa no tiene un certificado asociado.');
        }
        $ruc = $empresa->certificado->ruc;
        // Verificar que el RUC tenga 11 dígitos y comience con los prefijos deseados
        if (strlen($ruc) == 11 && preg_match('/^(10|20|17|21|22|30)/', $ruc) && !empty($certificado->certificado)) {
            // Si se cumple la condición, obtenemos todos los comprobantes
            $comprobantes = Comprobante::all();
        } else {
            // Si no se cumple la condición, obtenemos solo las "Notas de Venta"
            $comprobantes = Comprobante::where('tipo_comprobante', 'NOTA VENTA')->get();
        }
        //añadimos "ventas" para mostrar numero_comprobante
        return view('venta.create', [
            'productosPrecargados' => $productosPrecargados,
            // Otros datos necesarios para la vista
        ], compact('productos', 'clientes', 'comprobantes', 'numeroComprobante', 'metodosPago'));
    }

    // generar codigo unico para edintificador web 
    public function generarCodigoUnico()
    {
        do {
            // Genera un código alfanumérico de 10 caracteres
            $identificador_web = Str::random(20);
        } while (Venta::where('identificador_web', $identificador_web)->exists());

        return $identificador_web;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentaRequest $request)
    {
        // Verificar que el array de productos no esté vacío
        if (empty($request->arrayidproducto) && empty($request->arraynombre)) {
            return redirect()->back()->with('error', 'Agregue productos.');
        }

        try {
            DB::beginTransaction();

            // Obtener datos validados del formulario
            $validatedData = $request->validated();

            // Generar el identificador único de la venta (antes de cualquier validación)
            $identificador_web = $this->generarCodigoUnico();

            // Asignar el ID del usuario a la venta
            $validatedData['user_id'] = $request->user_id; // Asegurarse de que el ID del usuario esté presente

            // Verificar si la venta proviene de una orden
            // Log::info("Generando identificador_web para la venta: $identificador_web");
            if ($request->has('codigoOrden')) {
                // Si solo está presente 'codigoOrden'
                $codigoOrden = $request->codigoOrden;
            
                // Buscar la orden por el código de orden
                $orden = Orden::where('codigo_orden', $codigoOrden)->first();
                if (!$orden) {
                    return redirect()->back()->with('error', 'La orden no existe o no es válida.');
                }
            
                // Realizar las operaciones para la orden
                $orden->identificador_web_venta = $identificador_web; // Asignar el valor
                $orden->estadoventa = 1; // Establecer el estado de la venta
                $orden->estado = 'completado'; // Establecer el estado de la orden
                $orden->save(); // Guardar la orden
            
            } elseif ($request->has('codigoMesa')) {
                // Si solo está presente 'codigoMesa'
                $codigoMesa = $request->codigoMesa;
            
                // Buscar la mesa por el código de mesa
                $mesa = Mesa::where('codigo_mesa', $codigoMesa)->first();
                if (!$mesa) {
                    return redirect()->back()->with('error', 'La mesa no existe o no es válida.');
                }
            
                // Vaciar todos los productos asociados a la mesa
                $mesa->productos()->detach();  // Eliminar todos los productos asignados a la mesa
                $mesa->estado = 0; // Cambiar el estado de la mesa a 'libre'
                $mesa->save(); // Guardar los cambios en la mesa
            }           

            // Manejar la serie de venta
            // Acceder correctamente al comprobante_id en el arreglo $validatedData
            $comprobante = Comprobante::find($validatedData['comprobante_id']);

            // Asignar una serie a la venta basado en el tipo de comprobante
            if ($comprobante) {
                switch ($comprobante->tipo_comprobante) {
                    case 'FACTURA':
                        $serie = 'F001';
                        break;
                    case 'BOLETA':
                        $serie = 'B002';
                        break;
                    case 'NOTA VENTA': // COMPROBANTE QUE NO ENVIARA A LA SUNAT
                        $serie = 'NT00';
                        break;
                    default:
                        // Manejar tipos de comprobante desconocidos
                        // throw new Exception('Tipo de comprobante desconocido: ' . $comprobante->tipo_comprobante);
                }
            } else {
                Log::error('Comprobante no encontrado.');
                throw new Exception('Comprobante no encontrado.');
            }

            // Generar un numero_comprobante único
            $numeroComprobante = (int)$validatedData['numero_comprobante']; // Convertir a entero

            // Incrementar hasta encontrar un numero_comprobante disponible
            while (Venta::where('numero_comprobante', $numeroComprobante)->exists()) {
                $numeroComprobante++; // Incrementar el número
            }

            // Asignar la serie directamente, no desde el request
            $validatedData['serie'] = $serie;

            // Asignar datos adicionales a $validatedData
            $validatedData['monto_recibido'] = $request->monto_recibido ?? 0;
            $validatedData['metodo_pago_id'] = $request->metodo_pagos; // Asignar método de pago
            $validatedData['numero_operacion'] = $request->numero_operacion ?? 0;
            $validatedData['numero_comprobante'] = $numeroComprobante; // Asignar número de comprobante
            // codigo unico para identificador_web de venta
            // $identificador_web ya ha sido asignado aquí
            $validatedData['identificador_web'] = $identificador_web; // Asignar número al identificador_web 


            // Crear la venta con todos los datos
            $venta = Venta::create($validatedData);

            // Procesar productos de base de datos
            if (!empty($request->arrayidproducto)) {
                foreach ($request->arrayidproducto as $key => $producto_id) {
                    $venta->productos()->attach($producto_id, [
                        'cantidad' => $request->arraycantidad[$key],
                        'precio_ventas' => $request->arrayprecioventa[$key],
                        'descuento' => $request->arraydescuento[$key],
                    ]);

                    // Actualizar el stock del producto
                    $producto = Producto::find($producto_id);
                    if ($producto) {
                        $stockActual = $producto->stock;
                        $cantidad = intval($request->arraycantidad[$key]);

                        // Calcula el nuevo stock sin restricción de negativo
                        $nuevoStock = $stockActual - $cantidad;

                        // Actualizar el stock en la base de datos
                        $producto->update(['stock' => $nuevoStock]);
                    }
                }
            }

            // Procesar productos libres
            if (!empty($request->arraynombre)) {
                foreach ($request->arraynombre as $key => $nombre) {
                    $productoLibre = new Producto_Libre();
                    $productoLibre->venta_id = $venta->id;
                    // Verificar si el nombre está vacío y asignar "PRODUCTOS" si es así
                    $productoLibre->nombre = !empty(trim($nombre)) ? strtoupper($nombre) : 'PRODUCTOS';
                    $productoLibre->cantidad = $request->arraycantidad[$key];
                    $productoLibre->precio_venta = $request->arrayprecioventa[$key];
                    $productoLibre->descuento = $request->arraydescuento[$key];
                    $productoLibre->save();
                }
            }

            // Confirmar la transacción
            DB::commit();

            // Manejar las opciones de emisión
            $comprobante = Comprobante::find($venta->comprobante_id);

            if ($comprobante) {
                switch ($comprobante->tipo_comprobante) {
                    case 'FACTURA':
                        $response = $this->emitirComprobanteFacturaElectronico($venta->id);
                        $responseData = json_decode($response->getContent(), true); // Decodificar en JSON

                        // Verifica si la respuesta es exitosa
                        if ($response->getStatusCode() !== 200) {
                            Log::error('Error al emitir FACTURA: ' . $response->getContent());
                            $numeroComprobante = $responseData['numero_comprobante'] ?? 'No disponible';

                            // Guardar mensaje de error limpio en la base de datos
                            $venta->ventasEstado()->create([
                                'serie' => $numeroComprobante,
                                'estado_respuesta_sunat' => $responseData['mensaje'] ?? 'Error desconocido',
                            ]);

                            return redirect()->route('ventas.create')->with('info', 'Venta registrada, FALLO ENVIO A SUNAT');
                        } else {
                            // Procesar respuesta exitosa
                            $numeroComprobante = $responseData['numero_comprobante'] ?? 'No disponible';

                            // Guardar estado exitoso en la base de datos
                            $venta->ventasEstado()->create([
                                'serie' => $numeroComprobante,
                                'estado_respuesta_sunat' => $responseData['estado'] ?? 'Sin estado',
                            ]);
                        }

                        break;

                    case 'BOLETA':
                        $response = $this->emitirComprobanteBoletaElectronico($venta->id);
                        $responseData = json_decode($response->getContent(), true); // Decodificar en JSON

                        // Verifica si la respuesta es exitosa
                        if ($response->getStatusCode() !== 200) {
                            Log::error('Error al emitir BOLETA: ' . $response->getContent());

                            // Guardar mensaje de error en la base de datos
                            $venta->ventasEstado()->create([
                                'serie' => $serie . '-' . $venta->numero_comprobante, // Concatenación de serie y numero_comprobante
                                'estado_respuesta_sunat' => ucfirst($responseData['mensaje'] ?? 'Error desconocido'), // Mensaje de error
                            ]);

                            return redirect()->route('ventas.create')->with('info', 'Venta registrada, FALLO ENVIO A SUNAT');
                        } else {
                            // Procesar respuesta exitosa
                            $numeroComprobante = $responseData['numero_comprobante'] ?? 'No disponible';

                            // Guardar estado exitoso en la base de datos
                            $venta->ventasEstado()->create([
                                'serie' => $serie . '-' . $venta->numero_comprobante, // Concatenación de serie y numero_comprobante
                                'estado_respuesta_sunat' => ucfirst($responseData['estado'] ?? 'Sin estado'), // Estado exitoso
                            ]);
                        }
                        break;



                    default:
                        // Log::error('Tipo de comprobante desconocido: ' . $comprobante->tipo_comprobante);
                        break;
                }
            } else {
                Log::error('Comprobante no encontrado para la emisión.');
            }

            // Redireccionar con mensaje de éxito en la venta
            return redirect()->route('ventas.create')->with('success', 'Venta registrada exitosamente');
        } catch (\Exception $e) {
            // Deshacer la transacción en caso de error
            DB::rollback();

            // Registrar el error en los logs
            Log::error('Error al guardar la venta: ' . $e->getMessage());

            // Redireccionar con mensaje de error
            return redirect()->back()->with('error', 'Intentelo de Nuevo');
        }
    }

    public function emitirComprobanteFacturaElectronico($venta_id)
    {
        try {
            // Iniciar transacción
            DB::beginTransaction();

            // Obtener la venta con sus relaciones
            $venta = Venta::with(['productos', 'productosLibres', 'cliente.persona', 'comprobante', 'emisionComprobante'])
                ->findOrFail($venta_id);

            // Log::info('Empezando a emitir comprobante para venta ID: ' . $venta_id);

            // Obtener el comprobante relacionado
            $comprobante = $venta->comprobante;

            if (!$comprobante) {
                throw new Exception('Comprobante no encontrado para la venta.');
            }

            // Obtener el tipo de comprobante y la serie
            $tipoComprobante = strtolower($comprobante->tipo_comprobante); // 'factura', 'boleta', etc.
            $serie = $venta->serie;

            // Verificar que 'serie' no sea null
            if (is_null($serie)) {
                throw new Exception('La serie de la venta es nula.');
            }

            // Log::info('Tipo Comprobante: ' . $tipoComprobante . ', Serie: ' . $serie);

            // Obtener el siguiente correlativo disponible
            $nextCorrelativo = CorrelativoUsado::getNextCorrelativo($tipoComprobante, $serie);

            // Log::info('Correlativo siguiente: ' . $nextCorrelativo);

            // Crear un registro en correlativos_usados
            $correlativoUsado = CorrelativoUsado::create([
                'tipo_comprobante' => $tipoComprobante,
                'serie' => $serie,
                'correlativo' => $nextCorrelativo,
                'fecha' => now(),
            ]);

            // Log::info('Correlativo creado: ' . json_encode($correlativoUsado));

            // Asignar el correlativo a la venta
            $venta->correlativo = $nextCorrelativo;
            $venta->save();

            // Log::info('Venta actualizada con correlativo: ' . $nextCorrelativo);

            // Crear un registro en EmisionComprobante y asociarlo a la venta
            $emisionComprobante = EmisionComprobante::create([
                'comprobante_id' => $comprobante->id,
                'venta_id' => $venta->id,
                'serie' => $serie,
                'correlativo' => $nextCorrelativo,
                'fecha_emision' => now(),
                'hash_cpe' => null,
                'estado_sunat' => 'pendiente',
                'firma_digital' => null,
            ]);

            // Log::info('EmisionComprobante creado: ' . json_encode($emisionComprobante));

            // Obtener la empresa
            $empresa = Empresa::with('certificado')->first(); // Ajusta según tu lógica

            if (!$empresa) {
                throw new Exception('Empresa no encontrada.');
            }

            // Configurar Greenter con el certificado de la empresa
            $certificado = $empresa->certificado;

            if (!$certificado) {
                throw new Exception('Certificado de la empresa no encontrado.');
            }

            // Configurar Greenter
            // Configurar Greenter usando la función creada
            $see = $this->configurarGreenter($certificado);
            // Log::info('Greenter configurado con el certificado: ' . $certificado->certificado);

            // Cliente
            $cliente = $venta->cliente;
            if (!$cliente) {
                throw new Exception('Cliente no encontrado.');
            }

            $persona = $cliente->persona;
            if (!$persona) {
                throw new Exception('Persona del cliente no encontrada.');
            }

            // Crear objeto Address para el cliente
            $clientAddress = new Address();
            $clientAddress->setDireccion($persona->direccion ?: '-');

            $client = new Client();
            $client->setTipoDoc($persona->tipo_documento) // '1' para DNI, '6' para RUC
                ->setNumDoc($persona->numero_documento)
                ->setRznSocial($persona->razon_social)
                ->setAddress($clientAddress);

            // Log::info('Cliente configurado: ' . $persona->razon_social . 'doc:' . $persona->numero_documento . 'tipo doc:' . $persona->tipo_documento . 'direccion:' . $persona->direccion);

            // Emisor
            $address = new Address();
            $address->setUbigueo($empresa->ubigeo) // Asegúrate de tener este dato
                ->setDepartamento($empresa->departamento)
                ->setProvincia($empresa->provincia)
                ->setDistrito($empresa->distrito)
                ->setUrbanizacion('-')
                ->setDireccion($empresa->direccion_empresa)
                ->setCodLocal('0000'); // Código de establecimiento de SUNAT

            $company = new Company();
            $company->setRuc($empresa->ruc_dni)
                ->setRazonSocial($empresa->razon_social)
                ->setNombreComercial($empresa->razon_social_comprobante)
                ->setAddress($address);

            // Log::info('Emisor configurado: ' . $empresa->razon_social . 'ruc/dni: ' . $empresa->ruc_dni . 'nombre comercial: ' . $empresa->razon_social_comprobante
            //     . 'ubig/dep/prov/dist:' . $empresa->ubigeo . $empresa->departamento . $empresa->provincia . $empresa->distrito);

            // Factura
            $invoice = new Invoice();
            $invoice->setUblVersion('2.1')
                ->setTipoOperacion('0101') // Venta interna
                ->setTipoDoc('01') // Factura electrónica
                ->setSerie($serie)
                ->setCorrelativo($nextCorrelativo)
                ->setFechaEmision(new \DateTime($venta->fecha_hora, new \DateTimeZone('America/Lima')))
                ->setFormaPago(new FormaPagoContado())
                ->setTipoMoneda('PEN') // Soles
                ->setCompany($company)
                ->setClient($client);

            // Inicializar acumuladores de totales
            $valorVentaTotal = round($venta->total - $venta->impuesto, 2);
            $igvTotal = round($venta->impuesto, 2);
            $importeTotal = round($venta->total, 2);

            // Log::info('Totales obtenidos de la venta: Valor Venta: ' . $valorVentaTotal . ', IGV: ' . $igvTotal . ', Importe Total: ' . $importeTotal);

            $details = [];

            // Calcular el importe total de los productos para determinar las proporciones
            $totalItemsAmount = 0;

            // Procesar productos de base de datos
            foreach ($venta->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $precioVenta = $producto->pivot->precio_ventas; // Precio unitario con IGV
                $descuento = $producto->pivot->descuento; // Descuento total (por cantidad)
                $importeProducto = ($precioVenta * $cantidad) - $descuento;
                $totalItemsAmount += $importeProducto;
            }

            // Procesar productos libres
            foreach ($venta->productosLibres as $productoLibre) {
                $cantidad = $productoLibre->cantidad;
                $precioVenta = $productoLibre->precio_venta;
                $descuento = $productoLibre->descuento;
                $importeProducto = ($precioVenta * $cantidad) - $descuento;
                $totalItemsAmount += $importeProducto;
            }

            // Log::info('Importe total de los productos: ' . $totalItemsAmount);

            // Variable para ajustar el último detalle
            $ultimoDetalle = null;

            // Procesar productos de base de datos
            foreach ($venta->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $precioVenta = $producto->pivot->precio_ventas;
                $descuento = $producto->pivot->descuento;
                $importeProducto = ($precioVenta * $cantidad) - $descuento;

                // Calcular la proporción del producto en el total de la venta
                $proporcion = $importeProducto / $totalItemsAmount;

                // Calcular valores proporcionales
                $valorVentaProducto = round($valorVentaTotal * $proporcion, 2);
                $igvProducto = round($igvTotal * $proporcion, 2);
                $importeTotalProducto = round($importeTotal * $proporcion, 2);

                // Calcular valores unitarios
                $valorUnitario = round($valorVentaProducto / $cantidad, 8);
                $precioUnitarioComprobante = round($importeTotalProducto / $cantidad, 8);

                // **Agregar registro en el log para este producto**
                // Log::info('Procesando producto:', [
                //     'codigo' => $producto->codigo,
                //     'nombre' => $producto->nombre,
                //     'cantidad' => $cantidad,
                //     'precio_venta' => $precioVenta,
                //     'descuento' => $descuento,
                //     'importe_producto' => $importeProducto,
                //     'proporcion' => $proporcion,
                //     'valor_venta_producto' => $valorVentaProducto,
                //     'igv_producto' => $igvProducto,
                //     'importe_total_producto' => $importeTotalProducto,
                //     'valor_unitario' => $valorUnitario,
                //     'precio_unitario_comprobante' => $precioUnitarioComprobante
                // ]);

                // Crear el detalle
                $item = new SaleDetail();
                $item->setCodProducto($producto->codigo)
                    ->setUnidad('NIU')
                    ->setCantidad($cantidad)
                    ->setDescripcion($producto->nombre)
                    ->setMtoValorUnitario($valorUnitario)
                    ->setMtoValorVenta($valorVentaProducto)
                    ->setMtoBaseIgv($valorVentaProducto)
                    ->setPorcentajeIgv(18.00)
                    ->setIgv($igvProducto)
                    ->setTipAfeIgv('10') // Gravado - Operación Onerosa
                    ->setTotalImpuestos($igvProducto)
                    ->setMtoPrecioUnitario($precioUnitarioComprobante);

                $details[] = $item;
                $ultimoDetalle = $item; // Guardamos el último detalle para ajustar luego
            }

            // Procesar productos libres
            foreach ($venta->productosLibres as $productoLibre) {
                $cantidad = $productoLibre->cantidad;
                $precioVenta = $productoLibre->precio_venta;
                $descuento = $productoLibre->descuento;
                $importeProducto = ($precioVenta * $cantidad) - $descuento;

                // Calcular la proporción del producto en el total de la venta
                $proporcion = $importeProducto / $totalItemsAmount;

                // Calcular valores proporcionales
                $valorVentaProducto = round($valorVentaTotal * $proporcion, 2);
                $igvProducto = round($igvTotal * $proporcion, 2);
                $importeTotalProducto = round($importeTotal * $proporcion, 2);

                // Calcular valores unitarios
                $valorUnitario = round($valorVentaProducto / $cantidad, 8);
                $precioUnitarioComprobante = round($importeTotalProducto / $cantidad, 8);

                // **Agregar registro en el log para este producto libre**
                // Log::info('Procesando producto libre:', [
                //     'nombre' => $productoLibre->nombre,
                //     'cantidad' => $cantidad,
                //     'precio_venta' => $precioVenta,
                //     'descuento' => $descuento,
                //     'importe_producto' => $importeProducto,
                //     'proporcion' => $proporcion,
                //     'valor_venta_producto' => $valorVentaProducto,
                //     'igv_producto' => $igvProducto,
                //     'importe_total_producto' => $importeTotalProducto,
                //     'valor_unitario' => $valorUnitario,
                //     'precio_unitario_comprobante' => $precioUnitarioComprobante
                // ]);

                // Crear el detalle
                $item = new SaleDetail();
                $item->setCodProducto('') // Puedes asignar un código genérico si lo deseas
                    ->setUnidad('NIU')
                    ->setCantidad($cantidad)
                    ->setDescripcion($productoLibre->nombre)
                    ->setMtoValorUnitario($valorUnitario)
                    ->setMtoValorVenta($valorVentaProducto)
                    ->setMtoBaseIgv($valorVentaProducto)
                    ->setPorcentajeIgv(18.00)
                    ->setIgv($igvProducto)
                    ->setTipAfeIgv('10')
                    ->setTotalImpuestos($igvProducto)
                    ->setMtoPrecioUnitario($precioUnitarioComprobante);

                $details[] = $item;
                $ultimoDetalle = $item; // Guardamos el último detalle para ajustar luego
            }

            // Verificar que haya al menos un detalle
            if (empty($details)) {
                throw new Exception('La venta no tiene productos asociados.');
            }

            // Ajustar los detalles para que sumen exactamente los totales
            $sumaValorVentaDetalles = array_reduce($details, function ($carry, $item) {
                return $carry + $item->getMtoValorVenta();
            }, 0);

            $sumaIGVDetalles = array_reduce($details, function ($carry, $item) {
                return $carry + $item->getIgv();
            }, 0);

            $sumaImporteTotalDetalles = array_reduce($details, function ($carry, $item) {
                return $carry + ($item->getMtoPrecioUnitario() * $item->getCantidad());
            }, 0);

            // Ajustar las diferencias debido al redondeo
            $diferenciaValorVenta = $valorVentaTotal - $sumaValorVentaDetalles;
            $diferenciaIGV = $igvTotal - $sumaIGVDetalles;
            $diferenciaImporteTotal = $importeTotal - $sumaImporteTotalDetalles;

            // Log::info('Diferencias después del cálculo: Valor Venta: ' . $diferenciaValorVenta . ', IGV: ' . $diferenciaIGV . ', Importe Total: ' . $diferenciaImporteTotal);

            // Ajustar el último detalle para corregir las diferencias
            if ($ultimoDetalle) {
                $ultimoDetalle->setMtoValorVenta($ultimoDetalle->getMtoValorVenta() + $diferenciaValorVenta);
                $ultimoDetalle->setIgv($ultimoDetalle->getIgv() + $diferenciaIGV);
                $ultimoDetalle->setTotalImpuestos($ultimoDetalle->getTotalImpuestos() + $diferenciaIGV);
                $nuevoPrecioUnitario = ($ultimoDetalle->getMtoValorVenta() + $ultimoDetalle->getIgv()) / $ultimoDetalle->getCantidad();
                $ultimoDetalle->setMtoPrecioUnitario(round($nuevoPrecioUnitario, 8));
            }

            // Asignar detalles al invoice
            $invoice->setDetails($details);

            // Asignar totales al invoice
            $invoice->setMtoOperGravadas($valorVentaTotal)
                ->setMtoIGV($igvTotal)
                ->setTotalImpuestos($igvTotal)
                ->setValorVenta($valorVentaTotal)
                ->setSubTotal($importeTotal)
                ->setMtoImpVenta($importeTotal);

            // Agregar leyenda
            $legend = new Legend();
            $legend->setCode('1000')
                ->setValue('SON ' . $this->numberToWords($importeTotal) . ' SOLES');

            $invoice->setLegends([$legend]);

            // Registrar el monto total incluyendo IGV en los logs
            // Log::info('Monto total incluyendo IGV enviado a SUNAT: ' . $importeTotal);
            // Log::info('Factura configurada y lista para enviar a SUNAT.');

            // Enviar a SUNAT
            $result = $see->send($invoice);

            // Ruta de almacenamiento
            $path = storage_path('facturas/' . $venta->id);

            // Crear carpeta si no existe
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                // Log::info('Carpeta creada en: ' . $path);
            }

            // Guardar XML firmado
            File::put($path . '/' . $invoice->getName() . '.xml', $see->getFactory()->getLastXml());
            // Log::info('XML firmado guardado en: ' . $path . '/' . $invoice->getName() . '.xml');

            // Validar conexión con SUNAT
            if (!$result->isSuccess()) {
                // Manejar errores de SUNAT
                $error = $result->getError();
                throw new Exception('SUNAT Error: ' . $error->getCode() . ' - ' . $error->getMessage());
            }

            // Log::info('Factura enviada exitosamente a SUNAT.');

            // Guardar CDR ZIP
            $cdrZipPath = $path . '/R-' . $invoice->getName() . '.zip';
            File::put($cdrZipPath, $result->getCdrZip());
            // Log::info('CDR ZIP guardado en: ' . $cdrZipPath);

            // Extraer contenido del ZIP
            $zip = new ZipArchive;
            $cdrXmlContent = '';
            $cdrXmlFileName = 'R-' . $invoice->getName() . '.xml'; // Nombre del archivo XML

            if ($zip->open($cdrZipPath) === TRUE) {
                if ($zip->locateName($cdrXmlFileName) !== false) {
                    $zip->extractTo($path, $cdrXmlFileName);
                    $cdrXmlContent = File::get($path . '/' . $cdrXmlFileName);
                    // Log::info('CDR XML extraído y guardado en: ' . $path . '/' . $cdrXmlFileName);
                }
                $zip->close();
            } else {
                throw new Exception('No se pudo abrir el ZIP de CDR.');
            }

            // Calcular hash del CDR
            $hash = hash('sha1', $cdrXmlContent);
            // Log::info('Hash del CDR calculado: ' . $hash);

            // Lectura del CDR
            $cdr = $result->getCdrResponse();
            $code = (int)$cdr->getCode();

            // Determinar el estado del comprobante
            if ($code === 0) {
                $message = 'aceptada';
            } elseif ($code >= 2000 && $code <= 3999) {
                $message = 'rechazada';
            } else {
                $message = 'excepción - código inválido';
            }

            // Guardar CDR en la base de datos
            Cdr::create([
                'emision_comprobante_id' => $emisionComprobante->id,
                'estado' => strtolower($message),
                'codigo_respuesta' => (string)$code,
                'mensaje' => $cdr->getDescription(),
                'archivo_cdr' => 'R-' . $invoice->getName() . '.xml',
                'archivo_zip' => 'R-' . $invoice->getName() . '.zip',
                'contenido_xml' => $cdrXmlContent,
                'fecha_respuesta' => now(),
            ]);

            // Log::info('CDR guardado en la base de datos.');

            // Actualizar estado de la emisión
            $emisionComprobante->update([
                'estado_sunat' => strtolower($message),
                'hash_cpe' => $hash,
            ]);

            // Log::info('Estado de la emisión actualizado a: ' . strtolower($message));

            // Confirmar la transacción
            DB::commit();
            //tomar serie y numero comprobante para enviar 
            $numeroComprobante = ($serie && $venta->numero_comprobante) ? $serie . '-' . $venta->numero_comprobante : 'No disponible';

            // Devolver respuesta exitosa
            return response()->json([
                'estado' => ucfirst($message),
                'descripcion' => $cdr->getDescription(),
                'cdr_xml' => $cdrXmlContent,
                'hash' => $hash,
                'numero_comprobante' => $numeroComprobante, // Agregar "SERIE-CORRELATIVO" en la respuesta JSON
            ]);
        } catch (Exception $e) {
            // Deshacer la transacción en caso de error
            DB::rollback();
            //tomar serie y numero comprobante para enviar 
            $numeroComprobante = ($serie && $venta->numero_comprobante) ? $serie . '-' . $venta->numero_comprobante : 'No disponible';

            // Registrar el error en los logs
            // Log::error('Error al emitir factura: ' . $e->getMessage());

            // Devolver respuesta de error
            return response()->json([
                'estado' => 'Error',
                'mensaje' => $e->getMessage(),
                'numero_comprobante' => $numeroComprobante, // Agregar "SERIE-CORRELATIVO" en la respuesta JSON
            ], 500);
        }
    }

    public function emitirComprobanteBoletaElectronico($venta_id)
    {
        try {
            // Iniciar transacción
            DB::beginTransaction();

            // Obtener la venta con sus relaciones
            $venta = Venta::with([
                'productos',
                'productosLibres',
                'cliente.persona',
                'comprobante',
                'emisionComprobante'
            ])->findOrFail($venta_id);

            // Log::info('Empezando a emitir boleta para venta ID: ' . $venta_id);

            // Obtener el comprobante relacionado
            $comprobante = $venta->comprobante;

            if (!$comprobante) {
                throw new Exception('Comprobante no encontrado para la venta.');
            }

            // Obtener el tipo de comprobante y la serie
            $tipoComprobante = strtolower($comprobante->tipo_comprobante); // 'factura', 'boleta', etc.
            $serie = $venta->serie;

            // Verificar que 'serie' no sea null
            if (is_null($serie)) {
                throw new Exception('La serie de la venta es nula.');
            }

            // Log::info('Tipo Comprobante: ' . $tipoComprobante . ', Serie: ' . $serie);

            // Obtener el siguiente correlativo disponible
            $nextCorrelativo = CorrelativoUsado::getNextCorrelativo($tipoComprobante, $serie);

            // Log::info('Correlativo siguiente: ' . $nextCorrelativo);

            // Crear un registro en correlativos_usados
            $correlativoUsado = CorrelativoUsado::create([
                'tipo_comprobante' => $tipoComprobante,
                'serie' => $serie,
                'correlativo' => $nextCorrelativo,
                'fecha' => now(),
            ]);

            // Log::info('Correlativo creado: ' . json_encode($correlativoUsado));

            // Asignar el correlativo a la venta
            $venta->correlativo = $nextCorrelativo;
            $venta->save();

            // Log::info('Venta actualizada con correlativo: ' . $nextCorrelativo);

            // Crear un registro en EmisionComprobante y asociarlo a la venta
            $emisionComprobante = EmisionComprobante::create([
                'comprobante_id' => $comprobante->id,
                'venta_id' => $venta->id,
                'serie' => $serie,
                'correlativo' => $nextCorrelativo,
                'fecha_emision' => now(),
                'hash_cpe' => null,
                'estado_sunat' => 'pendiente',
                'firma_digital' => null,
            ]);

            // Log::info('EmisionComprobante creado: ' . json_encode($emisionComprobante));

            // Obtener la empresa
            $empresa = Empresa::with('certificado')->first(); // Ajusta según tu lógica

            if (!$empresa) {
                throw new Exception('Empresa no encontrada.');
            }

            // Configurar Greenter con el certificado de la empresa
            $certificado = $empresa->certificado;

            if (!$certificado) {
                throw new Exception('Certificado de la empresa no encontrado.');
            }

            // Configurar Greenter
            // Configurar Greenter usando la función creada
            $see = $this->configurarGreenter($certificado);
            // Log::info('Greenter configurado con el certificado: ' . $certificado->certificado);

            // Cliente
            $cliente = $venta->cliente;
            $persona = $cliente ? $cliente->persona : null;

            if (!$persona) {
                // Si no hay cliente asociado, usar datos genéricos para consumidor final
                $persona = new \stdClass();
                $persona->numero_documento = '-';
                $persona->razon_social = 'CLIENTE GENERICO';
                $persona->direccion = '-';
            }

            // Determinar el tipo de documento del cliente
            $numeroDocumento = $persona->numero_documento;
            $documentoLength = strlen($numeroDocumento);

            if ($documentoLength == 8) {
                $tipoDocCliente = '1'; // DNI
            } elseif ($documentoLength == 11) {
                $tipoDocCliente = '6'; // RUC
            } else {
                $tipoDocCliente = '-'; // Sin documento
            }

            // Crear objeto Address para el cliente
            $clientAddress = new Address();
            $clientAddress->setDireccion($persona->direccion ?: '-');

            $client = new Client();
            $client->setTipoDoc($tipoDocCliente)
                ->setNumDoc($numeroDocumento)
                ->setRznSocial($persona->razon_social)
                ->setAddress($clientAddress);

            // Log::info('Cliente configurado: ' . $persona->razon_social . ' con TipoDoc: ' . $tipoDocCliente .
            //     'numero documento ' . $numeroDocumento . 'direccion:' . $persona->direccion);

            // Emisor
            $address = new Address();
            $address->setUbigueo($empresa->ubigeo) // Asegúrate de tener este dato
                ->setDepartamento($empresa->departamento)
                ->setProvincia($empresa->provincia)
                ->setDistrito($empresa->distrito)
                ->setUrbanizacion('-')
                ->setDireccion($empresa->direccion_empresa)
                ->setCodLocal('0000'); // Código de establecimiento de SUNAT

            $company = new Company();
            $company->setRuc($empresa->ruc_dni)
                ->setRazonSocial($empresa->razon_social)
                ->setNombreComercial($empresa->razon_social_comprobante)
                ->setAddress($address);

            // Log::info('Emisor configurado: ' . $empresa->razon_social . 'ruc/dni: ' . $empresa->ruc_dni . 'nombre comercial: ' . $empresa->razon_social_comprobante
            //     . 'ubig/dep/prov/dist:' . $empresa->ubigeo . $empresa->departamento . $empresa->provincia . $empresa->distrito);

            // Boleta
            $invoice = new Invoice();
            $invoice->setUblVersion('2.1')
                ->setTipoOperacion('0101') // Venta interna
                ->setTipoDoc('03') // Boleta electrónica
                ->setSerie($serie)
                ->setCorrelativo($nextCorrelativo)
                ->setFechaEmision(new \DateTime($venta->fecha_hora, new \DateTimeZone('America/Lima')))
                ->setFormaPago(new FormaPagoContado())
                ->setTipoMoneda('PEN') // Soles
                ->setCompany($company)
                ->setClient($client);

            // Inicializar acumuladores de totales
            $valorVentaTotal = round($venta->total - $venta->impuesto, 2);
            $igvTotal = round($venta->impuesto, 2);
            $importeTotal = round($venta->total, 2);

            // Log::info('Totales obtenidos de la venta: Valor Venta: ' . $valorVentaTotal . ', IGV: ' . $igvTotal . ', Importe Total: ' . $importeTotal);

            $details = [];

            // Calcular el importe total de los productos para determinar las proporciones
            $totalItemsAmount = 0;

            // Procesar productos de base de datos
            foreach ($venta->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $precioVenta = $producto->pivot->precio_ventas; // Precio unitario con IGV
                $descuento = $producto->pivot->descuento; // Descuento total (por cantidad)
                $importeProducto = ($precioVenta * $cantidad) - $descuento;
                $totalItemsAmount += $importeProducto;
            }

            // Procesar productos libres
            foreach ($venta->productosLibres as $productoLibre) {
                $cantidad = $productoLibre->cantidad;
                $precioVenta = $productoLibre->precio_venta;
                $descuento = $productoLibre->descuento;
                $importeProducto = ($precioVenta * $cantidad) - $descuento;
                $totalItemsAmount += $importeProducto;
            }

            // Log::info('Importe total de los productos: ' . $totalItemsAmount);

            // Variable para ajustar el último detalle
            $ultimoDetalle = null;

            // Procesar productos de base de datos
            foreach ($venta->productos as $producto) {
                $cantidad = $producto->pivot->cantidad;
                $precioVenta = $producto->pivot->precio_ventas;
                $descuento = $producto->pivot->descuento;
                $importeProducto = ($precioVenta * $cantidad) - $descuento;

                // Calcular la proporción del producto en el total de la venta
                $proporcion = $importeProducto / $totalItemsAmount;

                // Calcular valores proporcionales
                $valorVentaProducto = round($valorVentaTotal * $proporcion, 2);
                $igvProducto = round($igvTotal * $proporcion, 2);
                $importeTotalProducto = round($importeTotal * $proporcion, 2);

                // Calcular valores unitarios
                $valorUnitario = round($valorVentaProducto / $cantidad, 8);
                $precioUnitarioComprobante = round($importeTotalProducto / $cantidad, 8);

                // Agregar registro en el log para este producto
                // Log::info('Procesando producto:', [
                //     'codigo' => $producto->codigo,
                //     'nombre' => $producto->nombre,
                //     'cantidad' => $cantidad,
                //     'precio_venta' => $precioVenta,
                //     'descuento' => $descuento,
                //     'importe_producto' => $importeProducto,
                //     'proporcion' => $proporcion,
                //     'valor_venta_producto' => $valorVentaProducto,
                //     'igv_producto' => $igvProducto,
                //     'importe_total_producto' => $importeTotalProducto,
                //     'valor_unitario' => $valorUnitario,
                //     'precio_unitario_comprobante' => $precioUnitarioComprobante
                // ]);

                // Crear el detalle
                $item = new SaleDetail();
                $item->setCodProducto($producto->codigo)
                    ->setUnidad('NIU')
                    ->setCantidad($cantidad)
                    ->setDescripcion($producto->nombre)
                    ->setMtoValorUnitario($valorUnitario)
                    ->setMtoValorVenta($valorVentaProducto)
                    ->setMtoBaseIgv($valorVentaProducto)
                    ->setPorcentajeIgv(18.00)
                    ->setIgv($igvProducto)
                    ->setTipAfeIgv('10') // Gravado - Operación Onerosa
                    ->setTotalImpuestos($igvProducto)
                    ->setMtoPrecioUnitario($precioUnitarioComprobante);

                $details[] = $item;
                $ultimoDetalle = $item; // Guardamos el último detalle para ajustar luego
            }

            // Procesar productos libres
            foreach ($venta->productosLibres as $productoLibre) {
                $cantidad = $productoLibre->cantidad;
                $precioVenta = $productoLibre->precio_venta;
                $descuento = $productoLibre->descuento;
                $importeProducto = ($precioVenta * $cantidad) - $descuento;

                // Calcular la proporción del producto en el total de la venta
                $proporcion = $importeProducto / $totalItemsAmount;

                // Calcular valores proporcionales
                $valorVentaProducto = round($valorVentaTotal * $proporcion, 2);
                $igvProducto = round($igvTotal * $proporcion, 2);
                $importeTotalProducto = round($importeTotal * $proporcion, 2);

                // Calcular valores unitarios
                $valorUnitario = round($valorVentaProducto / $cantidad, 8);
                $precioUnitarioComprobante = round($importeTotalProducto / $cantidad, 8);

                // Agregar registro en el log para este producto libre
                // Log::info('Procesando producto libre:', [
                //     'nombre' => $productoLibre->nombre,
                //     'cantidad' => $cantidad,
                //     'precio_venta' => $precioVenta,
                //     'descuento' => $descuento,
                //     'importe_producto' => $importeProducto,
                //     'proporcion' => $proporcion,
                //     'valor_venta_producto' => $valorVentaProducto,
                //     'igv_producto' => $igvProducto,
                //     'importe_total_producto' => $importeTotalProducto,
                //     'valor_unitario' => $valorUnitario,
                //     'precio_unitario_comprobante' => $precioUnitarioComprobante
                // ]);

                // Crear el detalle
                $item = new SaleDetail();
                $item->setCodProducto('') // Puedes asignar un código genérico si lo deseas
                    ->setUnidad('NIU')
                    ->setCantidad($cantidad)
                    ->setDescripcion($productoLibre->nombre)
                    ->setMtoValorUnitario($valorUnitario)
                    ->setMtoValorVenta($valorVentaProducto)
                    ->setMtoBaseIgv($valorVentaProducto)
                    ->setPorcentajeIgv(18.00)
                    ->setIgv($igvProducto)
                    ->setTipAfeIgv('10')
                    ->setTotalImpuestos($igvProducto)
                    ->setMtoPrecioUnitario($precioUnitarioComprobante);

                $details[] = $item;
                $ultimoDetalle = $item; // Guardamos el último detalle para ajustar luego
            }

            // Verificar que haya al menos un detalle
            if (empty($details)) {
                throw new Exception('La venta no tiene productos asociados.');
            }

            // Ajustar los detalles para que sumen exactamente los totales
            $sumaValorVentaDetalles = array_reduce($details, function ($carry, $item) {
                return $carry + $item->getMtoValorVenta();
            }, 0);

            $sumaIGVDetalles = array_reduce($details, function ($carry, $item) {
                return $carry + $item->getIgv();
            }, 0);

            $sumaImporteTotalDetalles = array_reduce($details, function ($carry, $item) {
                return $carry + ($item->getMtoPrecioUnitario() * $item->getCantidad());
            }, 0);

            // Ajustar las diferencias debido al redondeo
            $diferenciaValorVenta = $valorVentaTotal - $sumaValorVentaDetalles;
            $diferenciaIGV = $igvTotal - $sumaIGVDetalles;
            $diferenciaImporteTotal = $importeTotal - $sumaImporteTotalDetalles;

            // Log::info('Diferencias después del cálculo: Valor Venta: ' . $diferenciaValorVenta . ', IGV: ' . $diferenciaIGV . ', Importe Total: ' . $diferenciaImporteTotal);

            // Ajustar el último detalle para corregir las diferencias
            if ($ultimoDetalle) {
                $ultimoDetalle->setMtoValorVenta($ultimoDetalle->getMtoValorVenta() + $diferenciaValorVenta);
                $ultimoDetalle->setIgv($ultimoDetalle->getIgv() + $diferenciaIGV);
                $ultimoDetalle->setTotalImpuestos($ultimoDetalle->getTotalImpuestos() + $diferenciaIGV);
                $nuevoPrecioUnitario = ($ultimoDetalle->getMtoValorVenta() + $ultimoDetalle->getIgv()) / $ultimoDetalle->getCantidad();
                $ultimoDetalle->setMtoPrecioUnitario(round($nuevoPrecioUnitario, 8));
            }

            // Asignar detalles al invoice
            $invoice->setDetails($details);

            // Asignar totales al invoice
            $invoice->setMtoOperGravadas($valorVentaTotal)
                ->setMtoIGV($igvTotal)
                ->setTotalImpuestos($igvTotal)
                ->setValorVenta($valorVentaTotal)
                ->setSubTotal($importeTotal)
                ->setMtoImpVenta($importeTotal);

            // Agregar leyenda
            $legend = new Legend();
            $legend->setCode('1000')
                ->setValue('SON ' . $this->numberToWords($importeTotal) . ' SOLES');

            $invoice->setLegends([$legend]);

            // Registrar el monto total incluyendo IGV en los logs
            // Log::info('Monto total incluyendo IGV enviado a SUNAT: ' . $importeTotal);
            // Log::info('Boleta configurada y lista para enviar a SUNAT.');

            // Enviar a SUNAT
            $result = $see->send($invoice);

            // Ruta de almacenamiento
            $path = storage_path('boletas/' . $venta->id);

            // Crear carpeta si no existe
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                // Log::info('Carpeta creada en: ' . $path);
            }

            // Guardar XML firmado
            File::put($path . '/' . $invoice->getName() . '.xml', $see->getFactory()->getLastXml());
            // Log::info('XML firmado guardado en: ' . $path . '/' . $invoice->getName() . '.xml');

            // Validar conexión con SUNAT
            if (!$result->isSuccess()) {
                // Manejar errores de SUNAT
                $error = $result->getError();
                throw new Exception('SUNAT Error: ' . $error->getCode() . ' - ' . $error->getMessage());
            }

            // Log::info('Boleta enviada exitosamente a SUNAT.');

            // Guardar CDR ZIP
            $cdrZipPath = $path . '/R-' . $invoice->getName() . '.zip';
            File::put($cdrZipPath, $result->getCdrZip());
            // Log::info('CDR ZIP guardado en: ' . $cdrZipPath);

            // Extraer contenido del ZIP
            $zip = new ZipArchive;
            $cdrXmlContent = '';
            $cdrXmlFileName = 'R-' . $invoice->getName() . '.xml'; // Nombre del archivo XML

            if ($zip->open($cdrZipPath) === TRUE) {
                if ($zip->locateName($cdrXmlFileName) !== false) {
                    $zip->extractTo($path, $cdrXmlFileName);
                    $cdrXmlContent = File::get($path . '/' . $cdrXmlFileName);
                    // Log::info('CDR XML extraído y guardado en: ' . $path . '/' . $cdrXmlFileName);
                }
                $zip->close();
            } else {
                throw new Exception('No se pudo abrir el ZIP de CDR.');
            }

            // Calcular hash del CDR
            $hash = hash('sha1', $cdrXmlContent);
            // Log::info('Hash del CDR calculado: ' . $hash);

            // Lectura del CDR
            $cdr = $result->getCdrResponse();
            $code = (int)$cdr->getCode();

            // Determinar el estado del comprobante
            if ($code === 0) {
                $message = 'aceptada';
            } elseif ($code >= 2000 && $code <= 3999) {
                $message = 'rechazada';
            } else {
                $message = 'excepción - código inválido';
            }

            // Guardar CDR en la base de datos
            Cdr::create([
                'emision_comprobante_id' => $emisionComprobante->id,
                'estado' => strtolower($message),
                'codigo_respuesta' => (string)$code,
                'mensaje' => $cdr->getDescription(),
                'archivo_cdr' => 'R-' . $invoice->getName() . '.xml',
                'archivo_zip' => 'R-' . $invoice->getName() . '.zip',
                'contenido_xml' => $cdrXmlContent,
                'fecha_respuesta' => now(),
            ]);

            // Log::info('CDR guardado en la base de datos.');

            // Actualizar estado de la emisión
            $emisionComprobante->update([
                'estado_sunat' => strtolower($message),
                'hash_cpe' => $hash,
            ]);

            // Log::info('Estado de la emisión actualizado a: ' . strtolower($message));

            // Confirmar la transacción
            DB::commit();
            //tomar serie y numero comprobante para enviar 
            $numeroComprobante = (!empty($serie) && !empty($venta->numero_comprobante))
                ? $serie . '-' . $venta->numero_comprobante
                : 'No disponible';

            // Devolver respuesta exitosa
            return response()->json([
                'estado' => ucfirst($message),
                'descripcion' => $cdr->getDescription(),
                'cdr_xml' => $cdrXmlContent,
                'hash' => $hash,
                'numero_comprobante' => $numeroComprobante, // Agregar "SERIE-CORRELATIVO" en la respuesta JSON
            ]);
        } catch (Exception $e) {
            // Deshacer la transacción en caso de error
            DB::rollback();
            //tomar serie y numero comprobante para enviar 
            $numeroComprobante = (!empty($serie) && !empty($venta->numero_comprobante))
                ? $serie . '-' . $venta->numero_comprobante
                : 'No disponible';

            // Registrar el error en los logs
            // Log::error('Error al emitir boleta: ' . $e->getMessage());

            // Devolver respuesta de error
            return response()->json([
                'estado' => 'Error',
                'mensaje' => $e->getMessage(),
                'numero_comprobante' => $numeroComprobante, // Agregar "SERIE-CORRELATIVO" en la respuesta JSON
            ], 500);
        }
    }

    /**
     * Convertir número a palabras (Soles).
     */
    private function numberToWords($number)
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');

        $entero = floor($number);
        $decimales = round(($number - $entero) * 100);

        return strtoupper($numberTransformer->toWords($entero)) . ' CON ' . str_pad($decimales, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
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
    public function destroy(Request $request, string $id)
    {
        // Validar que la venta exista
        $venta = Venta::with(['productos', 'comprobante', 'emisionComprobante'])->find($id);

        if (!$venta) {
            return response()->json(['mensaje' => 'Venta no encontrada'], 404);
        }

        // Verificar que la venta no haya sido previamente eliminada
        if ($venta->estado != 1) {
            return response()->json(['mensaje' => 'La venta ya ha sido eliminada'], 400);
        }

        // Obtener el tipo de comprobante
        $tipoComprobante = strtoupper($venta->comprobante->tipo_comprobante);

        // Obtener el motivo de la solicitud
        $motivo = $request->input('motivo', 'Anulación por solicitud del cliente');

        try {
            DB::beginTransaction();

            // ** Restaurar stock de los productos asociados **
            foreach ($venta->productos as $producto) {
                $cantidadVendida = $producto->pivot->cantidad; // Asumiendo uso de tabla pivot
                $producto->stock += $cantidadVendida;
                $producto->save(); // Guardar el cambio en la base de datos
            }

            // Cambiar el estado de la venta a "eliminada" si es NOTA VENTA
            if ($tipoComprobante === 'NOTA VENTA' || !isset($venta->emisionComprobante)) {
                $venta->estado = 0;
                $venta->save(); // Guardar el cambio en la base de datos
                DB::commit();
                return response()->json([
                    'icon' => 'success',
                    'estado' => 'Exitoso',
                    'descripcion' => 'Eliminado Correctamente.',
                ]);
            }

            // Verificar que el comprobante está en un estado válido para anulación
            if (!$venta->emisionComprobante || $venta->emisionComprobante->estado_sunat != 'aceptada') {
                throw new Exception('El comprobante no existe o no está en un estado válido para anulación.');
            }

            // Obtener la empresa y su certificado
            $empresa = Empresa::with('certificado')->first();
            if (!$empresa) {
                throw new Exception('Empresa no encontrada.');
            }

            $certificado = $empresa->certificado;
            if (!$certificado) {
                throw new Exception('Certificado de la empresa no encontrado.');
            }

            // Configurar Greenter usando la función creada
            $see = $this->configurarGreenter($certificado);

            // Diferenciar entre factura y boleta
            $correlativo = str_pad(1, 4, '0', STR_PAD_LEFT);
            if ($tipoComprobante === 'FACTURA') {
                // Baja de Factura (RA)
                $voided = new Voided();
                $voided->setCorrelativo($correlativo)
                    ->setFecGeneracion(new \DateTime($venta->comprobante->fecha_emision))
                    ->setFecComunicacion(new \DateTime())
                    ->setCompany($this->getCompany($empresa))
                    ->setDetails([$this->createVoidedDetail($venta, $motivo)]);

                $result = $see->send($voided);
            } elseif ($tipoComprobante === 'BOLETA') {
                // Resumen Diario de Boletas (RC)
                $summary = new Summary();
                $summary->setFecGeneracion(new \DateTime($venta->comprobante->fecha_emision))
                    ->setFecResumen(new \DateTime())
                    ->setCorrelativo($correlativo)
                    ->setCompany($this->getCompany($empresa))
                    ->setDetails([$this->createSummaryDocument($venta, $motivo)]);

                $result = $see->send($summary);
            } else {
                // Actualizar el estado de la venta
                $venta->update(['estado' => 0]);
                return response()->json([
                    'estado' => 'Exitoso',
                    'descripcion' => 'Eliminado Correctamente.',
                ]);
            }

            // Guardar XML de la solicitud de baja
            $filename = "{$certificado->ruc}-RA-" . now()->format('Ymd') . "-{$correlativo}.zip";
            $path = storage_path('bajas/' . $venta->id);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
            File::put($path . '/' . $filename, $see->getFactory()->getLastXml());

            // Verificar si la solicitud fue exitosa
            if (!$result->isSuccess()) {
                $error = $result->getError();
                throw new Exception('SUNAT Error: ' . $error->getCode() . ' - ' . $error->getMessage());
            }

            // Obtener el ticket para consulta posterior y guardar como pendiente
            $ticket = $result->getTicket();
            BajaComprobante::create([
                'emision_comprobante_id' => $venta->emisionComprobante->id,
                'motivo' => $motivo,
                'fecha_baja' => now(),
                'ticket_baja' => $ticket,
                'estado_sunat' => 'pendiente',
                'hash_cpe' => null,
            ]);

            // Actualizar el estado de la venta
            $venta->update(['estado' => 0]);

            DB::commit();

            return response()->json([
                'icon' => 'info',
                'estado' => 'Exitoso',
                'descripcion' => 'La baja se ha iniciado, y está pendiente de confirmación.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error durante el proceso de baja: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al procesar la baja y restaurar el stock.'], 500);
        }
    }

    /**
     * Configura el objeto Company para Greenter.
     */
    private function getCompany($empresa)
    {
        $address = new Address();
        $address->setUbigueo($empresa->ubigeo)
            ->setDepartamento($empresa->departamento)
            ->setProvincia($empresa->provincia)
            ->setDistrito($empresa->distrito)
            ->setUrbanizacion('-')
            ->setDireccion($empresa->direccion_empresa)
            ->setCodLocal('0000');

        $company = new Company();
        $company->setRuc($empresa->ruc_dni)
            ->setRazonSocial($empresa->razon_social)
            ->setNombreComercial($empresa->razon_social_comprobante)
            ->setAddress($address);

        return $company;
    }

    /**
     * Crea un detalle para la solicitud de baja de facturas.
     */
    private function createVoidedDetail($venta, $motivo)
    {
        $detail = new VoidedDetail();
        $detail->setTipoDoc('01') // Factura usa tipo 01 para baja
            ->setSerie($venta->serie)
            ->setCorrelativo($venta->numero_comprobante)
            ->setDesMotivoBaja($motivo);

        return $detail;
    }

    /**
     * Crea un documento para el Resumen Diario de Boletas.
     */
    private function createSummaryDocument($venta, $motivo)
    {
        $detail = new SummaryDetail();
        $detail->setTipoDoc('03') // Boleta usa tipo 03 en resumen diario
            ->setSerieNro($venta->serie . '-' . $venta->numero_comprobante)
            ->setEstado('3') // Estado 3 para baja de boleta
            ->setClienteTipo('1') // Tipo de documento de identidad del cliente (DNI)
            ->setClienteNro($venta->cliente->dni)
            ->setTotal($venta->total)
            ->setMtoOperGravadas($venta->total - $venta->impuesto)
            ->setMtoIGV($venta->impuesto);
        // Se elimina ->setMotivo($motivo);

        return $detail;
    }
}
