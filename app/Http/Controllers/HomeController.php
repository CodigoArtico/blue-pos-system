<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Empresa;
use App\Models\Marca;
use App\Models\Presentacion;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fechaHoy = Carbon::now()->format('Y-m-d');

        $fecha_hoy = date('Y-m-d');

        //Datos de las 8 cartas.
        $cantidad_ventas = Venta::whereDate('created_at', $fecha_hoy)->where('estado', 1)->count();
        $categorias = Categoria::whereHas('caracteristica', function ($query) {
            $query->where('estado', 1);
        })->count();
        $compras = Compra::whereDate('created_at', $fecha_hoy)->where('estado', 1)->count();
        $marcas = Marca::whereHas('caracteristica', function ($query) {
            $query->where('estado', 1);
        })->count();
        $presentaciones = Presentacion::whereHas('caracteristica', function ($query) {
            $query->where('estado', 1);
        })->count();
        $productos = Producto::where('estado', 1)->count();
        $proveedores = Proveedor::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->count();
        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->count();

        // Pasamos los datos de la venta para mostrar en el inicio
        $ventas = Venta::with(['comprobante', 'cliente.persona', 'user'])
            ->where('estado', 1)
            ->latest()
            ->get();

        // Saldo de los últimos 7 días
        $fechas = [];
        $totales = [];
        for ($i = 0; $i < 7; $i++) {
            $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
            $fechas[] = $fecha;
            $total = Venta::whereDate('created_at', $fecha)
                ->where('estado', 1)
                ->sum('total');
            $totales[] = $total;
        }

        // Cantidad de productos vendidos últimos 7 días
        $productosFechas = [];
        $productosTotales = [];
        for ($i = 0; $i < 7; $i++) {
            $fecha = Carbon::now()->subDays($i)->format('Y-m-d');
            $productosFechas[] = $fecha;
            $cantidad = DB::table('producto_ventas')
                ->join('ventas', 'producto_ventas.venta_id', '=', 'ventas.id')
                ->whereDate('ventas.created_at', $fecha)
                ->where('ventas.estado', 1)
                ->sum('producto_ventas.cantidad');
            $productosTotales[] = $cantidad;
        }

        // Cantidad de ventas por meses últimos 7 meses
        $ventasMeses = [];
        $ventasTotales = [];
        for ($i = 0; $i < 7; $i++) {
            $fecha = Carbon::now()->subMonths($i)->format('Y-m');
            $ventasMeses[] = $fecha;
            $cantidad = Venta::whereMonth('created_at', '=', Carbon::now()->subMonths($i)->month)
                ->whereYear('created_at', '=', Carbon::now()->subMonths($i)->year)
                ->where('estado', 1)
                ->count();
            $ventasTotales[] = $cantidad;
        }

        // Saldo por meses últimos 7 meses
        $saldoMeses = [];
        $saldoTotales = [];
        for ($i = 0; $i < 7; $i++) {
            $fecha = Carbon::now()->subMonths($i)->format('Y-m');
            $saldoMeses[] = $fecha;
            $total = Venta::whereMonth('created_at', '=', Carbon::now()->subMonths($i)->month)
                ->whereYear('created_at', '=', Carbon::now()->subMonths($i)->year)
                ->where('estado', 1)
                ->sum('total');
            $saldoTotales[] = $total;
        }

        //// Fecha de inicio y fin del mes actual
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        // Obtener ventas del mes actual agrupadas por método de pago
        $ventasPorMetodoPago = Venta::select('metodo_pagos.nombre', DB::raw('COUNT(ventas.id) as total_ventas'))
            ->join('metodo_pagos', 'ventas.metodo_pago_id', '=', 'metodo_pagos.id')
            ->where('ventas.estado', 1)
            ->whereBetween('ventas.created_at', [$inicioMes, $finMes])
            ->groupBy('metodo_pagos.nombre')
            ->get();

        // Establecer el idioma para mostrar el mes en español
        \Carbon\Carbon::setLocale('es');

        // Obtener el nombre del mes actual en español
        $mesmetodopago = Carbon::now()->isoFormat('MMMM YYYY'); // Ejemplo: "septiembre 2024"

        // Consultar los productos con stock crítico
        $productosStockCritico = Producto::where('stock', '<', 5)
            ->orderBy('stock', 'asc')
            ->get();


        // Calcular los últimos 7 meses hasta el mes actual
        $meses = collect(range(0, 6))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse()->values();
        // dd($meses);

        // Consulta para obtener el producto más vendido de cada mes con ventas activas (estado = 1)
        $productosPorMes = DB::table('producto_ventas')
            ->select(
                DB::raw("DATE_FORMAT(ventas.created_at, '%Y-%m') as mes_anio"),
                'productos.nombre',
                DB::raw('SUM(producto_ventas.cantidad) as cantidad_total')
            )
            ->join('ventas', 'ventas.id', '=', 'producto_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'producto_ventas.producto_id')
            ->where('ventas.estado', 1)
            ->whereIn(DB::raw("DATE_FORMAT(ventas.created_at, '%Y-%m')"), $meses->all())
            ->groupBy('mes_anio', 'productos.nombre')
            ->orderBy('mes_anio', 'desc')
            ->orderBy('cantidad_total', 'desc')
            ->get();

        // dd($productosPorMes);

        // Organizar los productos por cada mes
        $productosAgrupados = [];
        foreach ($meses as $mes) {
            $productoMes = $productosPorMes->firstWhere('mes_anio', $mes);
            $productosAgrupados[] = $productoMes
                ? ['nombre' => $productoMes->nombre, 'cantidad' => $productoMes->cantidad_total]
                : ['nombre' => 'N/A', 'cantidad' => 0];
        }
        // dd($productosAgrupados);

        $mesActual = Carbon::now()->format('Y-m');
        $mesAnterior = Carbon::now()->subMonth()->format('Y-m');

        // Monto total de ventas del mes actual
        $montoMesActual = DB::table('ventas')
            ->where('estado', 1)
            ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $mesActual)
            ->sum('total');

        // Monto total de ventas del mes anterior
        $montoMesAnterior = DB::table('ventas')
            ->where('estado', 1)
            ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $mesAnterior)
            ->sum('total');

        // Calcular el porcentaje de cambio entre el mes actual y el anterior
        $subidaPorcentaje = $montoMesAnterior > 0
            ? (($montoMesActual - $montoMesAnterior) / $montoMesAnterior) * 100
            : 100;

        // Producto más vendido del mes actual
        $productoMesActual = DB::table('producto_ventas')
            ->select('productos.nombre', DB::raw('SUM(producto_ventas.cantidad) as cantidad_total'))
            ->join('productos', 'productos.id', '=', 'producto_ventas.producto_id')
            ->join('ventas', 'ventas.id', '=', 'producto_ventas.venta_id')
            ->where('ventas.estado', 1)
            ->where(DB::raw("DATE_FORMAT(ventas.created_at, '%Y-%m')"), $mesActual)
            ->groupBy('productos.nombre')
            ->orderBy('cantidad_total', 'desc')
            ->first();

        // Producto más vendido del mes anterior
        $productoMesAnterior = DB::table('producto_ventas')
            ->select('productos.nombre', DB::raw('SUM(producto_ventas.cantidad) as cantidad_total'))
            ->join('productos', 'productos.id', '=', 'producto_ventas.producto_id')
            ->join('ventas', 'ventas.id', '=', 'producto_ventas.venta_id')
            ->where('ventas.estado', 1)
            ->where(DB::raw("DATE_FORMAT(ventas.created_at, '%Y-%m')"), $mesAnterior)
            ->groupBy('productos.nombre')
            ->orderBy('cantidad_total', 'desc')
            ->first();
        // Obtener el top anual de los usuarios que han realizado ventas en el año actual con estado=1
        $topUsuariosAnual = User::join('ventas', 'users.id', '=', 'ventas.user_id')
            ->join('producto_ventas', 'ventas.id', '=', 'producto_ventas.venta_id')
            ->select(
                'users.id',
                'users.name',
                'users.img',
                'users.estado',
                DB::raw('COUNT(producto_ventas.id) as total_ventas'),
                DB::raw('SUM(producto_ventas.precio_ventas) as monto_ventas')
            )
            ->whereYear('ventas.created_at', now()->year)
            ->where('ventas.estado', 1)
            ->groupBy('users.id', 'users.name', 'users.img', 'users.estado')
            ->orderBy('total_ventas', 'desc')
            ->limit(3)
            ->get();

        // Calcular el puntaje anual para cada usuario
        foreach ($topUsuariosAnual as $usuario) {
            $usuario->puntaje = ($usuario->total_ventas * 0.5) + ($usuario->monto_ventas * 0.5);
        }

        // Obtener los usuarios que han realizado ventas este mes con estado=1
        $topUsuarios = User::join('ventas', 'users.id', '=', 'ventas.user_id')
            ->join('producto_ventas', 'ventas.id', '=', 'producto_ventas.venta_id')
            ->select(
                'users.id',
                'users.name',
                'users.img',
                'users.img',
                'users.estado',
                DB::raw('COUNT(producto_ventas.id) as total_ventas'),
                DB::raw('SUM(producto_ventas.precio_ventas) as monto_ventas')
            )
            ->whereMonth('ventas.created_at', now()->month)
            ->whereYear('ventas.created_at', now()->year)
            ->where('ventas.estado', 1)
            ->groupBy('users.id', 'users.name', 'users.img', 'users.estado')
            ->orderBy('total_ventas', 'desc')
            ->limit(3)
            ->get();

        // Obtener las ventas por mes del año actual para los top 3 usuarios
        $topUsuariosIds = $topUsuarios->pluck('id'); // Obtener los IDs de los top 3 usuarios

        $ventasMensuales = Venta::join('producto_ventas', 'ventas.id', '=', 'producto_ventas.venta_id')
            ->whereIn('ventas.user_id', $topUsuariosIds)
            ->whereYear('ventas.created_at', now()->year)
            ->where('ventas.estado', 1)
            ->select('ventas.user_id', DB::raw('MONTH(ventas.created_at) as mes'), DB::raw('COUNT(ventas.id) as cantidad_total'))
            ->groupBy('ventas.user_id', 'mes')
            ->orderBy('ventas.user_id')
            ->orderBy('mes')
            ->get()
            ->groupBy('user_id'); // Agrupar por user_id

        // Obtener los top 5 clientes que más veces compraron en el año actual
        $topClientes = Cliente::join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->join('ventas', 'clientes.id', '=', 'ventas.cliente_id')
            ->select(
                'personas.razon_social as nombre',
                DB::raw('COUNT(ventas.id) as total_compras'),
                DB::raw('SUM(ventas.total) as monto_total'),
                DB::raw('(0.5 * COUNT(ventas.id) + 0.5 * SUM(ventas.total)) as score')
            )
            ->whereYear('ventas.fecha_hora', now()->year)
            ->where('ventas.estado', 1)
            ->groupBy('personas.razon_social')
            ->orderBy('score', 'desc') // Ordenamos por el puntaje calculado
            ->limit(5)
            ->get();

        return view('dashboard/dashboard', compact(
            'fechas',
            'totales',
            'productosFechas',
            'productosTotales',
            'ventasMeses',
            'ventasTotales',
            'saldoMeses',
            'saldoTotales',
            'fechaHoy',
            'ventas',
            'ventasPorMetodoPago',
            'mesmetodopago',
            'productosStockCritico',
            'productosAgrupados',
            'meses',
            'montoMesActual',
            'subidaPorcentaje',
            'productoMesActual',
            'productoMesAnterior',
            'topUsuarios',
            'ventasMensuales',
            'topClientes',
            'topUsuariosAnual',
            'cantidad_ventas',
            'categorias',
            'compras',
            'marcas',
            'presentaciones',
            'productos',
            'proveedores',
            'clientes'
        ));
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
