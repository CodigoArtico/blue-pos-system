<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Mesa;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Str;
use FPDF;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller as BaseController;

class MesasController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-mesas|detalle-mesas', ['only' => ['index']]);
        $this->middleware('role_or_permission:detalle-mesas', ['only' => ['show']]);
    }
    //
    /**
     * Mostrar todas las mesas disponibles.
     */
    public function index()
    {
        // Obtener todas las mesas
        $mesas = Mesa::all();

        // Obtener todos los productos con estado 1
        $productosActivos = Producto::where('estado', 1)->get();

        // Retornar la vista mesas.index y pasar las mesas y productos activos
        return view('mesas.index', compact('mesas', 'productosActivos'));
    }

    public function generarCodigoUnico()
    {
        do {
            // Genera un código alfanumérico de 10 caracteres
            $codigo = Str::random(10);
        } while (Producto::where('codigo', $codigo)->exists());

        return $codigo;
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|boolean',
        ]);
        // codigo unico para el producto 
        $codigo = $this->generarCodigoUnico();
        $mesa = Mesa::create([
            'codigo_mesa' => $codigo,
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mesa creada exitosamente.',
            'mesa' => $mesa
        ]);
    }

    public function show($id)
    {
        // Obtener la mesa junto con sus productos solo si están disponibles (estado = 1)
        $mesa = Mesa::with(['productos' => function ($query) {
            $query->where('estado', 1);
        }])->findOrFail($id);

        // Obtener productos que tengan compras activas con precio de venta o precio de compra
        $productos = Producto::where('estado', 1)
            ->whereHas('compras', function ($query) {
                // Asegurarse de que el estado de la compra sea 1 (activa) y que tenga un precio (de compra o de venta)
                $query->where('estado', 1)
                    ->where(function ($query) {
                        $query->whereNotNull('precio_venta')
                            ->orWhereNotNull('precio_compra');
                    });
            })
            ->with(['compras' => function ($query) {
                // Obtener solo la compra más reciente
                $query->latest()->take(1);
            }])
            ->get();

        return view('mesas.show', compact('mesa', 'productos'));
    }


    // Método para editar una mesa
    public function edit($id)
    {
        $mesa = Mesa::findOrFail($id);

        // Verifica que todos los datos estén siendo devueltos correctamente
        return response()->json([
            'id' => $mesa->id,
            'nombre' => $mesa->nombre,
            'estado' => $mesa->estado,
            'reserva' => $mesa->reserva,
            'nombre_reserva' => $mesa->nombre_reserva,
            'hora_reserva' => $mesa->hora_reserva
        ]);
    }

    // Método para actualizar una mesa
    public function update(Request $request, $id)
    {
        $mesa = Mesa::findOrFail($id);
        $mesa->nombre = $request->nombre;
        $mesa->estado = $request->estado;  // 1 para ocupada, 0 para libre
        $mesa->reserva = $request->reserva ? 1 : 0; // Si es reservada, estado de reserva se pone a 0
        if ($request->reserva) {
            $mesa->nombre_reserva = $request->nombre_reserva;
            $mesa->hora_reserva = $request->hora_reserva;
        } else {
            $mesa->nombre_reserva = null;
            $mesa->hora_reserva = null;
        }
        $mesa->save();

        return response()->json([
            'success' => true,
            'message' => 'Mesa actualizada correctamente.',
        ]);
    }
    public function addProduct(Request $request, $id)
    {
        $validatedData = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $mesa = Mesa::findOrFail($id);
        $producto = Producto::findOrFail($validatedData['producto_id']);

        // Verificar si el producto ya está asociado a la mesa
        if ($mesa->productos()->where('producto_id', $producto->id)->exists()) {
            return response()->json([
                'success' => false,
                'icon' => 'info', // Especifica el icono aquí
                'message' => 'El producto ya está agregado a la mesa.'
            ]);
        }

        // Añadir producto a la mesa
        $mesa->productos()->attach($producto, ['cantidad' => $validatedData['cantidad']]);

        // Cambiar el estado de la mesa a ocupada (1)
        $mesa->estado = 1; // Asumiendo que 1 significa ocupada
        $mesa->save();

        return response()->json(['success' => true, 'icon' => 'success', 'message' => 'Añadido correctamente.']);
    }

    public function removeProduct(Request $request, $mesaId, $productoId)
    {
        $mesa = Mesa::findOrFail($mesaId);
        $mesa->productos()->detach($productoId);

        // Verifica si la mesa tiene más productos
        if ($mesa->productos()->count() === 0) {
            // Cambia el estado de la mesa a libre (0)
            $mesa->estado = 0; // Asumiendo que 0 significa libre
            $mesa->save();
        }

        return response()->json(['success' => true, 'icon' => 'info', 'message' => 'Producto Quitado.']);
    }

    public function updateProduct(Request $request, $mesaId, $productoId)
    {
        $validatedData = $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $mesa = Mesa::findOrFail($mesaId);
        $mesa->productos()->updateExistingPivot($productoId, ['cantidad' => $validatedData['cantidad']]);

        return response()->json(['success' => true, 'icon' => 'success', 'message' => 'Cantidad Actualizada.']);
    }

    public function generarTicket($id)
    {
        // Buscar la mesa con sus productos asociados
        $mesa = Mesa::with('productos')->findOrFail($id);

        // Definir el tamaño personalizado (80 mm x 300 mm)
        $width = 80;  // Ancho en mm
        $height = 300; // Alto en mm

        // Iniciar FPDF con tamaño personalizado
        $pdf = new FPDF();
        $pdf->AddPage('P', [$width, $height]); // 'P' para orientación vertical

        // Ajustar márgenes
        $pdf->SetMargins(5, 8, 5);  // Márgenes de 5 mm a los lados y 8 mm arriba/abajo

        // Título del ticket, centrado
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'Ticket: ' . $mesa->nombre, 0, 1, 'C');
        $pdf->Ln(1); // Espacio

        // Estado de la mesa, centrado
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, 'Estado: ' . ($mesa->estado == 0 ? 'Disponible' : 'Ocupada') . ($mesa->reserva == 1 ? '- Reservada' : ''), 0, 1, 'C');
        $pdf->Ln(1); // Espacio
        $pdf->Cell(0, 5, 'CONSUMOS:', 0, 1, 'C');

        // Separador
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(70, 2, '---------------------------------------------------------------------------', 0, 1, 'L');

        // Encabezados de la tabla (Cant, Nombre, Unit, Total)
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(40, 4, mb_convert_encoding('Nombre', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        $pdf->Cell(9, 4, 'Cant', 0, 0, 'L');
        $pdf->Cell(10, 4, 'Unit', 0, 0, 'C');
        $pdf->Cell(10, 4, 'Total', 0, 1, 'C');

        // Segundo separador
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(70, 2, '---------------------------------------------------------------------------', 0, 1, 'L');
        $pdf->Ln(3); // Espacio

        // Inicializar variable para el total
        $totalGeneral = 0;

        // Listado de productos
        $pdf->SetFont('Arial', '', 8);

        foreach ($mesa->productos as $producto) {
            // Guardar la posición Y inicial antes de la descripción
            $yBeforeDescription = $pdf->GetY();

            // Nombre del producto en mayúsculas y soporte para saltos de línea
            $descripcionCompleta = mb_convert_encoding(strtoupper($producto->nombre), 'ISO-8859-1', 'UTF-8');
            $anchoDescripcion = 40; // Ajustado para el nombre

            // Mostrar el nombre del producto en la celda con salto de línea automático
            $pdf->MultiCell($anchoDescripcion, 5, $descripcionCompleta, 0, 'L');

            // Obtener la nueva posición Y después del MultiCell
            $yAfterDescription = $pdf->GetY();

            // Cantidad del producto
            $cantidad = $producto->pivot->cantidad;

            // Precio unitario y total
            $precioUnitario = $producto->precioVentaReciente();
            $total = $precioUnitario * $cantidad;
            $totalFormateado = number_format($total, 2, '.', ',');

            // Sumar al total general
            $totalGeneral += $total;

            // Posicionar las columnas de cantidad, precio unitario y total
            $pdf->SetXY($anchoDescripcion + 5, $yBeforeDescription); // Columna Cantidad
            $pdf->Cell(9, 5, $cantidad, 0, 0, 'L');

            $pdf->SetXY($anchoDescripcion + 15, $yBeforeDescription); // Columna Precio Unitario
            $pdf->Cell(10, 5, number_format($precioUnitario, 2, '.', ','), 0, 0, 'C');

            $pdf->SetXY($anchoDescripcion + 25, $yBeforeDescription); // Columna Total
            $pdf->Cell(10, 5, $totalFormateado, 0, 1, 'C');

            // Ajustar la posición Y para la siguiente fila
            $pdf->SetY($yAfterDescription + 2);
        }

        // Separador final
        $pdf->Ln(5); // Espacio
        $pdf->Cell(70, 2, '---------------------------------------------------------------------------', 0, 1, 'L');
        $pdf->Ln(3); // Espacio

        // Mostrar el importe total
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(75, 0, 'IMPORTE TOTAL: S/ ' . number_format($totalGeneral, 2, '.', ','), 0, 1, 'C');

        // Mensaje final, centrado
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Gracias por su visita!', 0, 1, 'C');

        // Descargar el PDF
        $pdf->Output();
        exit;
    }

    // Eliinar una Mesa
    public function destroy($id)
    {
        // Buscar la mesa por su ID
        $mesa = Mesa::findOrFail($id);

        // Eliminar las relaciones en la tabla intermedia (mesa_producto)
        $mesa->productos()->detach();

        // Eliminar la mesa
        $mesa->delete();

        // Retornar una respuesta JSON
        return response()->json([
            'success' => true,
            'message' => 'Mesa eliminada correctamente.',
        ]);
    }
}
