<?php

namespace App\Http\Controllers;

use App\Models\Billboard;
use App\Models\PageWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

use Illuminate\Routing\Controller as BaseController;

class BillboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-billboard', ['only' => ['index']]);
        $this->middleware('role_or_permission:editar-billboard', ['only' => ['store', 'update']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener el primer registro de PageWeb con sus billboards
        $pageWeb = PageWeb::with('billboards')->first();

        return view('billboard.index', compact('pageWeb'));
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
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
        ]);

        // Crear un nuevo billboard
        $billboard = new Billboard();
        $billboard->id_page = $request->input('id_page');
        $billboard->img_url = $request->input('img_url'); // Aquí podrías poner otra URL si fuera necesario

        // Subir la imagen y guardar su path
        if ($request->hasFile('image')) {
            $imageName = $billboard->handleUploadImage($request->file('image'));
            $billboard->img_path = $imageName;
        }

        $billboard->save();

        return redirect()->back()->with('success', 'Imagen subida y guardada correctamente.');
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
        // Validar la solicitud
        $request->validate([
            'url_sistem' => 'nullable|string|url',
            'url_web' => 'nullable|string|url',
            'telefono' => 'nullable|string',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'img_url.*' => 'nullable|string|url',
            'img_path.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado_url.*' => 'nullable|boolean',
            'estado_path.*' => 'nullable|boolean',
        ]);

        // Encontrar el registro de PageWeb
        $pageWeb = PageWeb::findOrFail($id);

        // Actualizar los datos de PageWeb
        $pageWeb->url_sistem = $request->input('url_sistem');
        $pageWeb->url_web = $request->input('url_web');
        $pageWeb->telefono = $request->input('telefono');
        $pageWeb->nombre = $request->input('nombre');
        $pageWeb->descripcion = $request->input('descripcion');
        $pageWeb->save();

        // Actualizar los billboards
        foreach ($pageWeb->billboards as $index => $billboard) {
            // Si hay una nueva URL de imagen
            if (isset($request->img_url[$index])) {
                $billboard->img_url = $request->img_url[$index];
            }

            // Actualizar el estado de la URL
            $billboard->estado_url = $request->estado_url[$index] ?? 0;

            // Si se sube una nueva imagen
            if ($request->hasFile("img_path.$index")) {
                try {
                    // Obtener el archivo de imagen
                    $image = $request->file("img_path.$index");

                    // Validar que el archivo es válido antes de continuar
                    if (!$image->isValid()) {
                        throw new \Exception('El archivo de imagen no es válido.');
                    }

                    // Generar un nombre único para la imagen
                    $originalName = preg_replace('/\s+/', '_', pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                    $name = time() . '_' . $originalName . '.webp';
                    $imagePath = 'billboards/' . $name;

                    // Convertir la imagen a WebP y guardarla en el almacenamiento público
                    $webpImage = Image::make($image)->encode('webp', 90);
                    Storage::disk('public')->put($imagePath, (string) $webpImage);

                    // Eliminar la imagen anterior si existe
                    if ($billboard->img_path) {
                        Storage::disk('public')->delete('billboards/' . $billboard->img_path);
                    }

                    // Asignar el nuevo nombre de imagen al billboard
                    $billboard->img_path = $name;
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error al procesar la imagen: ' . $e->getMessage()], 500);
                }
            }

            // Actualizar el estado del Path
            $billboard->estado_path = $request->estado_path[$index] ?? 0;

            // Guardar los cambios del billboard
            $billboard->save();
        }

        // Responder con éxito en formato JSON
        return response()->json([
            'success' => true,
            'message' => 'Página actualizada exitosamente.',
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
