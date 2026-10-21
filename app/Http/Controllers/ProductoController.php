<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacion;
use App\Models\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use Illuminate\Routing\Controller as BaseController;

class ProductoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-producto', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-producto', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-producto', ['only' => ['edit','update']]);
        $this->middleware('role_or_permission:eliminar-producto', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function generarCodigoUnico()
    {
        do {
            // Genera un código alfanumérico de 10 caracteres
            $codigo = Str::random(15);
        } while (Producto::where('codigo', $codigo)->exists());

        return $codigo;
    }
    public function index()
    {
        // Obtener todos los productos con sus relaciones cargadas
        $productos = Producto::with(['categorias.caracteristica', 'marca.caracteristica', 'presentacion.caracteristica'])
            ->latest()
            ->get();

        // codigo unico para el producto 
        $numeroCodigo = $this->generarCodigoUnico();

        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
            ->select('marcas.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $presentaciones = Presentacion::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
            ->select('presentaciones.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
            ->select('categorias.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();


        // Retornar la vista con los productos
        return view('producto.index', compact('productos', 'marcas', 'presentaciones', 'categorias', 'numeroCodigo'));
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
    public function store(StoreProductoRequest $request)
    {
        // Log::info('Método store iniciado.');
        Log::info('Datos de la solicitud:', $request->all());

        try {
            DB::beginTransaction();

            $producto = new Producto();

            if ($request->hasFile('img_path')) {
                // Log::info('Imagen recibida en la solicitud.');
                $name = $producto->handleUploadImage($request->file('img_path'));
                // Log::info('Imagen subida correctamente: ' . $name);
            } else {
                // Log::info('No se recibió ninguna imagen en la solicitud.');
                $name = null;
            }

            $producto->fill([
                'codigo' => $request->codigo,
                'nombre' => strtoupper($request->nombre),
                'stock' => $request->stock ?? 0,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'img_url' => $request->img_url,
                'medida' => $request->medida,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id,
                'destacado' => $request->destacado, // Aquí gestionamos el campo destacado
            ]);
            $producto->save();

            // Verificar que las categorías existen y están activas
            $categorias = Categoria::whereIn('id', $request->categorias)
                ->whereHas('caracteristica', function ($query) {
                    $query->where('estado', 1);
                })
                ->pluck('id')
                ->toArray();

            if (empty($categorias)) {
                throw new \Exception('No se encontraron categorías válidas.');
            }

            $producto->categorias()->attach($categorias);

            DB::commit();
            // Log::info('Transacción completada correctamente.');

            return response()->json([
                'success' => true,
                'message' => 'Producto registrado correctamente.',
                'producto' => $producto
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            // Log::error('Errores de validación: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error al guardar: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el producto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
            ->select('marcas.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $presentaciones = Presentacion::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
            ->select('presentaciones.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
            ->select('categorias.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        // Cargar la compra más reciente del producto
        $compraReciente = $producto->compras()->latest()->first();

        return view('producto.edit', compact('producto', 'marcas', 'presentaciones', 'categorias', 'compraReciente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {

        try {
            DB::beginTransaction();

            if ($request->hasFile('img_path')) {
                $name = $producto->handleUploadImage($request->file('img_path'));

                if (Storage::disk('public')->exists('productos/' . $producto->img_path)) {
                    Storage::disk('public')->delete('productos/' . $producto->img_path);
                }
            } else {
                $name = $producto->img_path;
            }

            $producto->fill([
                'codigo' => $request->codigo,
                'nombre' => strtoupper($request->nombre),
                'stock' => $request->stock,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'img_url' => $request->img_url,
                'medida' => $request->medida,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id,
                'destacado' => $request->has('destacado') ? 1 : 0, // Aquí gestionamos el campo destacado
            ]);

            $producto->save();

            $producto->categorias()->sync($request->get('categorias'));

            // Actualizar los precios de compra y venta de la compra más reciente
            if ($request->has('precios_compra')) {
                foreach ($request->input('precios_compra', []) as $compraId => $precioCompra) {
                    $compra = $producto->compras()->find($compraId);
                    if ($compra) {
                        $compra->pivot->precio_compra = $precioCompra;
                        $compra->pivot->precio_venta = $request->input('precios_venta.' . $compraId);
                        $compra->pivot->save();
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar el producto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el producto');
        }

        return redirect()->route('productos.index')->with('success', 'Producto editado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
        }

        // Alternar el estado del producto
        $producto->estado = $producto->estado == 1 ? 0 : 1;
        $producto->save();

        $message = $producto->estado == 1 ? 'Producto restaurado.' : 'Producto eliminado.';

        return response()->json(['success' => true, 'message' => $message]);
    }
}
