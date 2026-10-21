<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Producto;
use App\Models\Proveedor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class CompraController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-compras', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-compra', ['only' => ['store']]);
        $this->middleware('role_or_permission:eliminar-compra', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // Si la fecha final es menor que la fecha inicial, ajustamos la fecha final
        if ($endDate < $startDate) {
            $endDate = $startDate;
        }

        $compras = Compra::with(['comprobante', 'proveedor.persona'])
            // ->where('estado', 1)
            ->whereBetween('fecha_hora', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        // Calcular el monto total de las compras en el rango de fechas
        $totalCompras = Compra::where('estado', 1)
            ->whereBetween('fecha_hora', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total');


        // Obtener el número de comprobante más reciente, si existe
        $ultimoComprobante = Compra::latest('numero_comprobante')->pluck('numero_comprobante')->first();

        // Si no hay registros, establecer el número de comprobante inicial en 0
        $ultimoNumeroComprobante = $ultimoComprobante ?? 0;

        // Calcular el próximo número de comprobante sumándole 1 al último número
        $numeroComprobante = $ultimoNumeroComprobante + 1;

        $proveedores = Proveedor::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();
        $comprobantes = Comprobante::all();
        $productos = Producto::where('estado', 1)->get();

        return view('compra.index', compact('compras', 'startDate', 'endDate', 'totalCompras', 'proveedores', 'comprobantes', 'productos', 'numeroComprobante'));
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
        try {
            DB::beginTransaction();

            // Validar los datos
            $validated = $request->validate([
                'proveedore_id' => 'required|exists:proveedores,id',
                'comprobante_id' => 'required|exists:comprobantes,id',
                'numero_comprobante' => 'required|string|max:50',
                'impuesto' => 'required|numeric|min:0',
                'fecha_hora' => 'required|date',
                'arrayidproducto' => 'required|array|min:1',
                'arrayidproducto.*' => 'exists:productos,id',
                'arraycantidad' => 'required|array|min:1',
                'arraycantidad.*' => 'integer|min:1',
                'arraypreciocompra' => 'required|array|min:1',
                'arraypreciocompra.*' => 'numeric|min:0',
                'arrayprecioventa' => 'required|array|min:1',
                'arrayprecioventa.*' => 'numeric|min:0',
            ]);

            // Calcular el total
            $total = 0;
            foreach ($request->arraycantidad as $index => $cantidad) {
                $precioCompra = $request->arraypreciocompra[$index];
                $total += $cantidad * $precioCompra;
            }

            // Generar un numero_comprobante único
            $numeroComprobante = (int)$validated['numero_comprobante'];
            while (Compra::where('numero_comprobante', $numeroComprobante)->exists()) {
                $numeroComprobante++;
            }

            // Crear la compra con el total calculado
            $compra = Compra::create([
                'fecha_hora' => $validated['fecha_hora'],
                'impuesto' => $validated['impuesto'],
                'numero_comprobante' => $numeroComprobante,
                'total' => $total,
                'comprobante_id' => $validated['comprobante_id'],
                'proveedore_id' => $validated['proveedore_id'],
            ]);

            // Preparar los datos para la relación
            $productosSync = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = intval($request->arraycantidad[$index]);
                $precioCompra = floatval($request->arraypreciocompra[$index]);
                $precioVenta = floatval($request->arrayprecioventa[$index]);

                $productosSync[$productoId] = [
                    'cantidad' => $cantidad,
                    'precio_compra' => $precioCompra,
                    'precio_venta' => $precioVenta,
                ];

                $producto = Producto::findOrFail($productoId);
                $producto->increment('stock', $cantidad);
            }

            $compra->productos()->attach($productosSync);

            DB::commit();

            // Responder diferente según el tipo de solicitud
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Compra exitosa']);
            }

            return redirect()->route('compras.index')->with('success', 'Compra exitosa');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al almacenar la compra: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al almacenar la compra. Añada Productos'], 500);
            }

            return redirect()->back()->with('error', 'Añada Productos');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Compra $compra) {}

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
    // CompraController.php

    public function destroy(string $id)
    {
        $compra = Compra::find($id);

        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        $compra->update(['estado' => 0]);

        return response()->json(['message' => 'Compra eliminada exitosamente']);
    }
}
