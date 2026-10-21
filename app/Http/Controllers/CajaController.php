<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\MetodoPago;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;

class CajaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-caja|ver-gastos', ['only' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener las fechas de inicio y fin desde la solicitud
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Si no se proporcionan fechas, establecer el rango de los últimos 7 días
        if (!$startDate && !$endDate) {
            $endDate = Carbon::today()->toDateString(); // Fecha actual
            $startDate = Carbon::today()->subDays(6)->toDateString(); // Hace 7 días incluyendo hoy
        } elseif (!$startDate) {
            // Si solo falta la fecha de inicio, calcular 7 días atrás desde la fecha de fin
            $startDate = Carbon::parse($endDate)->subDays(6)->toDateString();
        } elseif (!$endDate) {
            // Si solo falta la fecha de fin, tomar la fecha de inicio y calcular 7 días adelante
            $endDate = Carbon::parse($startDate)->addDays(6)->toDateString();
        }

        // Convertir a objetos Carbon
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Obtener todas las fechas dentro del rango de fechas
        $fechas = Venta::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as fecha')
            ->groupBy('fecha')
            ->get();

        $resumenCaja = [];
        $ventasPorDia = [];
        $gastosPorDia = [];
        $saldoAcumuladoPorDia = [];
        $saldoAcumulado = 0;

        // Obtener el detalle de gastos del rango de fechas
        $gastosRango = Gasto::whereBetween('fecha', [$startDate, $endDate])->get();

        // Datos para el gráfico de métodos de pago
        $metodosPago = MetodoPago::all();
        $metodosLabels = $metodosPago->pluck('nombre');
        $metodosData = $metodosLabels->map(function ($nombre) use ($startDate, $endDate) {
            return Gasto::whereBetween('created_at', [$startDate, $endDate])  // Filtrar por fechas
                ->where('estado', 1) // Filtrar por estado 1
                ->whereHas('metodoPago', function ($query) use ($nombre) {
                    $query->where('nombre', $nombre);  // Filtrar por el nombre del método de pago
                })
                ->count();  // Obtener la cantidad de gastos por método de pago
        });

        // Recorrer las fechas y obtener el total de ventas y gastos para cada día
        foreach ($fechas as $fecha) {
            // Ventas en efectivo
            $ventasEfectivo = Venta::whereDate('created_at', $fecha->fecha)
                ->where('estado', 1)
                ->whereHas('metodoPago', function ($query) {
                    $query->where('nombre', 'Efectivo');
                })
                ->sum('total');

            // Ventas en otros métodos de pago
            $ventasOtros = Venta::whereDate('created_at', $fecha->fecha)
                ->where('estado', 1)
                ->whereHas('metodoPago', function ($query) {
                    $query->where('nombre', '<>', 'Efectivo');
                })
                ->sum('total');

            // Gastos en efectivo
            $gastosEfectivo = Gasto::whereDate('fecha', $fecha->fecha)
                ->where('estado', 1)
                ->whereHas('metodoPago', function ($query) {
                    $query->where('nombre', 'Efectivo');
                })
                ->sum('monto');

            // Gastos en otros métodos de pago
            $gastosOtros = Gasto::whereDate('fecha', $fecha->fecha)
                ->where('estado', 1)
                ->whereHas('metodoPago', function ($query) {
                    $query->where('nombre', '<>', 'Efectivo');
                })
                ->sum('monto');

            // Cálculo del saldo efectivo y otros
            $saldoEfectivo = $ventasEfectivo - $gastosEfectivo;
            $saldoOtros = $ventasOtros - $gastosOtros;

            // Suma total
            $saldoTotal = $saldoEfectivo + $saldoOtros;

            // Acumular el saldo total por día
            $saldoAcumulado += $saldoTotal;
            $saldoAcumuladoPorDia[] = $saldoAcumulado;

            // Registrar ventas y gastos para los gráficos
            $ventasPorDia[] = $ventasEfectivo + $ventasOtros;
            $gastosPorDia[] = $gastosEfectivo + $gastosOtros;

            // Detalles de gastos
            $gastosDetalles = Gasto::whereDate('fecha', $fecha->fecha)->get();

            $resumenCaja[] = [
                'fecha' => $fecha->fecha,
                'ventas_efectivo' => $ventasEfectivo,
                'ventas_otros' => $ventasOtros,
                'gastos_efectivo' => $gastosEfectivo,
                'gastos_otros' => $gastosOtros,
                'saldo_efectivo' => $saldoEfectivo,
                'saldo_otros' => $saldoOtros,
                'saldo_total' => $saldoTotal,
                'gastos_detalle' => $gastosDetalles,
            ];
        }
        // Calcular el total de ingresos y gastos en el rango de fechas
        $totalIngresos = Venta::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 1)
            ->sum('total');

        $totalGastos = Gasto::whereBetween('fecha', [$startDate, $endDate])
            ->where('estado', 1)
            ->sum('monto');


        // Pasar los resultados a la vista
        return view('caja.index', [
            'resumenCaja' => $resumenCaja,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'gastosRango' => $gastosRango, // Detalle de todos los gastos en el rango
            'metodosPago' => $metodosPago,
            'metodosLabels' => $metodosLabels,
            'metodosData' => $metodosData,
            'fechas' => $fechas->pluck('fecha'),
            'ventasPorDia' => $ventasPorDia,
            'gastosPorDia' => $gastosPorDia,
            'saldoAcumuladoPorDia' => $saldoAcumuladoPorDia,
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
        ]);
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
