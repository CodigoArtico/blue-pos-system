<?php

namespace App\Http\Controllers;

use App\Exports\ProductosExport;
use App\Models\Cliente;
use App\Models\CompraProducto;
use App\Models\Comprobante;
use App\Models\Empresa;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Producto_Libre;
use App\Models\ProductoVenta;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Routing\Controller as BaseController;

class InformeController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:informes', ['only' => ['index', 'export', 'export_pdf']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener la fecha de inicio y fin de la solicitud
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Si no se proporcionan fechas, establecer un rango de los últimos 7 días
        if (!$startDate && !$endDate) {
            $endDate = now()->format('Y-m-d'); // Fecha actual
            $startDate = now()->subDays(6)->format('Y-m-d'); // Hace 7 días incluyendo hoy
        } elseif (!$startDate) {
            // Si solo falta la fecha de inicio, calcular 7 días atrás desde la fecha de fin
            $startDate = Carbon::parse($endDate)->subDays(6)->format('Y-m-d');
        } elseif (!$endDate) {
            // Si solo falta la fecha de fin, calcular 7 días adelante desde la fecha de inicio
            $endDate = Carbon::parse($startDate)->addDays(6)->format('Y-m-d');
        }

        // Asegurar que se consideren las 24 horas del día
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // Obtener todos los productos, con sus relaciones necesarias
        $productos = Producto::with(['categorias.caracteristica', 'marca.caracteristica', 'presentacion.caracteristica'])
            ->latest()
            ->get();

        // Inicializar los arreglos para almacenar los datos calculados
        $ventas_por_producto = [];
        $margen_por_producto = [];
        $porcentaje_ganancia_por_producto = [];
        $ventas_totales_por_producto = [];
        $stock_por_producto = [];

        // Procesar cada producto
        foreach ($productos as $producto) {
            // Obtener todas las ventas del producto dentro del rango de fechas
            $ventas = Venta::whereHas('productos', function ($query) use ($producto) {
                $query->where('producto_id', $producto->id);
            })
                ->where('estado', 1)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->get();

            // Sumar la cantidad de cada venta del producto
            $cantidad_total_ventas = $ventas->sum(function ($venta) use ($producto) {
                return $venta->productos->where('id', $producto->id)->first()->pivot->cantidad ?? 0;
            });

            // Obtener el precio de compra del producto más reciente
            $compra_producto = CompraProducto::where('producto_id', $producto->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $precio_compra = $compra_producto ? $compra_producto->precio_compra : 0;

            // Obtener el precio de venta más reciente del producto
            $precio_venta = $producto->precioVentaReciente();

            // Calcular el margen de ganancia
            $margen_ganancia = $precio_venta - $precio_compra;

            // Calcular el porcentaje de ganancia
            $porcentaje_ganancia = $precio_compra > 0 ? ($margen_ganancia / $precio_compra) * 100 : 0;

            // Obtener el stock disponible
            $stock_disponible = $producto->stock;

            // Calcular el total de ventas en el rango de fechas
            $ventas_totales = $cantidad_total_ventas * $precio_venta;

            // Guardar los datos calculados
            $ventas_por_producto[$producto->id] = $cantidad_total_ventas;
            $margen_por_producto[$producto->id] = $margen_ganancia;
            $porcentaje_ganancia_por_producto[$producto->id] = $porcentaje_ganancia;
            $ventas_totales_por_producto[$producto->id] = $ventas_totales;
            $stock_por_producto[$producto->id] = $stock_disponible;
        }

        // Obtener los registros de ProductoVenta en el rango de fechas
        $productoVentas = ProductoVenta::with(['producto', 'venta'])
            ->whereHas('venta', function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
            })
            ->get();

        // Obtener todos los usuarios con estado '1'
        $usuarios = User::where('estado', 1)->get();

        // Arreglos para almacenar los datos de los usuarios
        $userNames = [];
        $userTotalVentas = [];
        $userMontoVentas = [];
        $userPuntos = [];

        // Procesar cada usuario para obtener las métricas
        foreach ($usuarios as $usuario) {
            $ventas = Venta::where('user_id', $usuario->id)
                ->where('estado', 1)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->get();

            if ($ventas->isNotEmpty()) {
                $numeroVentas = $ventas->count();
                $totalMontoVentas = $ventas->sum('total');
                $puntaje = $numeroVentas * 0.5 + $totalMontoVentas * 0.5;

                $userNames[] = $usuario->name;
                $userTotalVentas[] = $numeroVentas;
                $userMontoVentas[] = $totalMontoVentas;
                $userPuntos[] = $puntaje;
            }
        }

        // Obtener todos los clientes activos (estado 1)
        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();

        $clientNames = [];
        $clientTotalVentas = [];
        $clientMontoVentas = [];
        $clientPuntos = [];

        foreach ($clientes as $cliente) {
            $ventas = $cliente->ventas()->where('estado', 1)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->get();

            if ($ventas->isNotEmpty()) {
                $numeroVentas = $ventas->count();
                $totalMontoVentas = $ventas->sum('total');
                $puntaje = $numeroVentas * 0.5 + $totalMontoVentas * 0.5;

                $clientNames[] = $cliente->persona->razon_social;
                $clientTotalVentas[] = $numeroVentas;
                $clientMontoVentas[] = $totalMontoVentas;
                $clientPuntos[] = $puntaje;
            }
        }

        // Top clientes: aplicar límite de 8
        $topClients = collect($clientNames)->zip($clientTotalVentas, $clientMontoVentas, $clientPuntos)
            ->sortByDesc(fn($item) => $item[3])
            ->take(8);

        // Top usuarios: sin límite
        $topUsers = collect($userNames)->zip($userTotalVentas, $userMontoVentas, $userPuntos)
            ->sortByDesc(fn($item) => $item[3]);

        // Obtener los métodos de pago y las ventas asociadas en el rango de fechas
        $metodosPago = MetodoPago::with(['ventas' => function ($query) use ($startDateTime, $endDateTime) {
            $query->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('estado', 1);
        }])->get();

        // Calcular la cantidad de ventas por método de pago
        $metodosLabels = [];
        $metodosData = [];
        foreach ($metodosPago as $metodo) {
            $metodosLabels[] = $metodo->nombre; // Nombre del método
            $metodosData[] = $metodo->ventas->count(); // Cantidad de ventas
        }

        // Obtener los comprobantes con la cantidad de ventas asociadas
        $comprobantes = Comprobante::withCount(['ventas' => function ($query) use ($startDateTime, $endDateTime) {
            $query->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->where('estado', 1); // Solo ventas activas
        }])->get();

        // Calcular etiquetas y datos para el gráfico
        $comprobanteLabels = $comprobantes->pluck('tipo_comprobante')->toArray();
        $comprobanteData = $comprobantes->pluck('ventas_count')->toArray();

        // Obtener todos los productos libres vendidos en el rango de fechas
        $productoLibres = Producto_Libre::whereHas('venta', function ($query) use ($startDateTime, $endDateTime) {
            $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
        })->with('venta.user')  // Cargar la relación 'venta' y su 'user'
            ->get();

        // Inicializar los arreglos para las métricas
        $productoLibresCantidad = [$productoLibres->sum('cantidad')];  // Sumar la cantidad de todos los productos libres vendidos
        $productoLibresMontoVendido = [$productoLibres->sum(function ($productoLibre) {
            return $productoLibre->cantidad * $productoLibre->precio_venta; // Sumar el monto total vendido
        })];

        return view('informe.index',  [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'productoLibresCantidad' => $productoLibresCantidad,
            'productoLibresMontoVendido' => $productoLibresMontoVendido,
            'clientNames' => $topClients->pluck(0),
            'clientTotalVentas' => $topClients->pluck(1),
            'clientMontoVentas' => $topClients->pluck(2),
            'clientPuntos' => $topClients->pluck(3),
            'userNames' => $topUsers->pluck(0),
            'userTotalVentas' => $topUsers->pluck(1),
            'userMontoVentas' => $topUsers->pluck(2),
            'userPuntos' => $topUsers->pluck(3),
        ], compact(
            'productoLibres',
            'productos',
            'ventas_por_producto',
            'margen_por_producto',
            'porcentaje_ganancia_por_producto',
            'ventas_totales_por_producto',
            'stock_por_producto',
            'productoVentas',
            'metodosLabels',
            'metodosData',
            'comprobanteLabels',
            'comprobanteData'
        ));
    }

    //AQUI EXPORTAMOS EL REPORTE A EXCEL
    public function export(Request $request)
    {
        // Obtener las fechas del request
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        // dd($startDate, $endDate);

        // Obtener todos los productos
        $productos = Producto::with(['categorias.caracteristica', 'marca.caracteristica', 'presentacion.caracteristica'])
            ->latest()
            ->get();

        // Calcular el número de ventas de cada producto en el rango de fechas
        $ventas_por_producto = [];
        foreach ($productos as $producto) {
            // Obtener todas las ventas del producto dentro del rango de fechas
            $ventas = Venta::whereHas('productos', function ($query) use ($producto) {
                $query->where('producto_id', $producto->id);
            })
                ->where('estado', 1)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']) // Filtrar por rango de fechas
                ->get();

            // Sumar la cantidad de cada venta del producto
            $cantidad_total_ventas = $ventas->sum(function ($venta) use ($producto) {
                return $venta->productos->where('id', $producto->id)->first()->pivot->cantidad ?? 0;
            });

            // Guardar la cantidad total de ventas para este producto
            $ventas_por_producto[$producto->id] = $cantidad_total_ventas;
        }

        // Ordenar productos por ventas
        $productosOrdenados = $productos->sortByDesc(function ($producto) use ($ventas_por_producto) {
            return $ventas_por_producto[$producto->id] ?? 0;
        });

        // Pasar los productos y las ventas a la exportación
        $export = new ProductosExport($productosOrdenados, $ventas_por_producto, $startDate, $endDate);

        // Descargar el archivo Excel
        return Excel::download($export, 'productos.xlsx');
    }

    //EXPORTAMOS A PDF
    public function export_pdf(Request $request)
    {
        // Obtener las fechas del request
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Obtener todos los productos
        $productos = Producto::with(['categorias.caracteristica', 'marca.caracteristica', 'presentacion.caracteristica'])
            ->latest()
            ->get();

        // Calcular el número de ventas de cada producto en el rango de fechas
        $ventas_por_producto = [];
        foreach ($productos as $producto) {
            // Obtener todas las ventas del producto dentro del rango de fechas
            $ventas = Venta::whereHas('productos', function ($query) use ($producto) {
                $query->where('producto_id', $producto->id);
            })
                ->where('estado', 1)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']) // Filtrar por rango de fechas
                ->get();

            // Sumar la cantidad de cada venta del producto
            $cantidad_total_ventas = $ventas->sum(function ($venta) use ($producto) {
                return $venta->productos->where('id', $producto->id)->first()->pivot->cantidad ?? 0;
            });

            // Guardar la cantidad total de ventas para este producto
            $ventas_por_producto[$producto->id] = $cantidad_total_ventas;
        }

        // Ordenar los productos por ventas
        $productosOrdenados = $productos->sortByDesc(function ($producto) use ($ventas_por_producto) {
            return $ventas_por_producto[$producto->id] ?? 0;
        });

        // Obtener los datos de la empresa
        $empresa = Empresa::first();

        // Ruta del logo por defecto si no hay uno en la base de datos
        $defaultLogoPath = public_path('images/banner2.png');

        // Verificar si hay un logo en la base de datos; si no, usar el logo por defecto
        if (!empty($empresa->img_account) && file_exists(public_path('storage/img_account_empresa/' . $empresa->img_account))) {
            $logoPath = public_path('storage/img_account_empresa/' . $empresa->img_account);
        } else {
            $logoPath = $defaultLogoPath;
        }

        // Crear una copia temporal del logo para usar en el PDF
        $tempLogoPath = storage_path('app/public/img_account_empresa/temp_logo.png');
        copy($logoPath, $tempLogoPath); // Copiar el logo a una ruta temporal

        // Generar el PDF
        $pdf = PDF::loadView('informe.report_pdf', [
            'productos' => $productosOrdenados,
            'ventas_por_producto' => $ventas_por_producto,
            'startDate' => $startDate,  // Fechas añadidas
            'endDate' => $endDate,      // Fechas añadidas
            'logoPath' => $tempLogoPath, // Ruta temporal del logo para el PDF
        ]);

        // Obtener el contenido del PDF
        $output = $pdf->output();

        // Eliminar el archivo temporal después de generar el PDF
        if (file_exists($tempLogoPath)) {
            unlink($tempLogoPath);
        }

        // Retornar el PDF al navegador con encabezados para abrir en una nueva ventana
        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="reporte_productos.pdf"')  // Esto hace que se abra en una nueva ventana
            ->header('Cache-Control', 'private, max-age=0, must-revalidate')
            ->header('Expires', '0')
            ->header('Pragma', 'public');
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
        //
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
}
