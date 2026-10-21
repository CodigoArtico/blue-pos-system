<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Venta;
use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;

class OrdenController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:admin-pedidos', ['only' => ['index', 'mostrarVenta', 'getOrderDetails', 'cambiarEstado']]);
    }
    /**
     * Display a listing of the resource.
     */
    // Controlador
    public function index(Request $request)
    {
        // Obtener las fechas
        $startDate = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        // Configurar la zona horaria a Lima, Perú
        $timezone = 'America/Lima';
        // Si es una solicitud Ajax
        if ($request->ajax()) {
            $orders = Orden::with('user_web')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) use ($timezone) {
                    // Convertir la fecha a la zona horaria de Lima y formatearla
                    $order->created_at = $order->created_at->setTimezone($timezone)->toDateTimeString();
                    return $order;
                });

            return response()->json($orders); // Asegúrate de retornar la respuesta
        }

        // Para la vista normal
        $orders = Orden::with('user_web', 'productos')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orden.index', compact('orders', 'startDate', 'endDate'));
    }

    // OrderController.php

    public function mostrarVenta($orderId, $identificadorVenta)
    {
        // Buscar la orden por su ID
        $orden = Orden::findOrFail($orderId);

        // Verificar si la orden tiene el identificador de venta
        if ($orden->identificador_web_venta == $identificadorVenta) {
            // Buscar la venta correspondiente con el identificador_web
            $venta = Venta::where('identificador_web', $identificadorVenta)->first();

            if ($venta) {
                return response()->json([
                    'identificador_web' => $venta->identificador_web,
                    'total' => $venta->total,
                    'fecha_hora' => $venta->fecha_hora,
                    'cliente' => $venta->cliente, // Suponiendo que tienes una relación de cliente
                    'estado' => $venta->estado
                ]);
            } else {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }
        } else {
            return response()->json(['error' => 'Identificador de venta no coincide con la orden'], 404);
        }
    }


    public function getOrderDetails($id)
    {
        $order = Orden::with('user_web', 'productos')->findOrFail($id);
        return response()->json($order);
    }


    public function cambiarEstado(Request $request, $id)
    {
        $orden = Orden::findOrFail($id);
        $nuevoEstado = $request->input('estado');

        $orden->estado = $nuevoEstado;
        $orden->save();

        return response()->json(['success' => true, 'mensaje' => 'Estado actualizado correctamente.']);
    }

    public function contarOrdenes(Request $request)
    {
        // Obtener las fechas (si es necesario)
        $startDate = $request->input('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Contar las órdenes entre las fechas especificadas
        $orderCount = Orden::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Comprobar si hay alguna orden con estado "pendiente"
        $hasPendingOrder = Orden::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('estado', 'pendiente')
            ->exists();

        return response()->json([
            'orderCount' => $orderCount,
            'hasPendingOrder' => $hasPendingOrder // Enviar la bandera de "pendiente"
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
