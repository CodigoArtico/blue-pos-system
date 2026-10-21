<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

use Illuminate\Routing\Controller as BaseController;

class ProfileController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-perfil', ['only' => ['index']]);
        $this->middleware('role_or_permission:editar-perfil', ['only' => ['update']]);
    }

    public function index()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        return view('profile.index', compact('user')); // Retornar los datos a la vista
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request) {}

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Definir reglas de validación
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8', // Contraseña opcional con longitud mínima
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Máximo 2MB
        ];

        // Validar datos
        $validatedData = $request->validate($rules);

        try {
            // Actualizar nombre y email
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            // Actualizar contraseña si se envía
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            // Verificar si se ha subido una nueva imagen
            if ($request->hasFile('img')) {
                // Subir la nueva imagen utilizando el método del modelo
                $imageFile = $request->file('img');
                $imageName = $user->handleUploadImage($imageFile);

                // Opcional: Eliminar la imagen anterior si existe
                if ($user->img && Storage::disk('public')->exists($user->img)) {
                    Storage::disk('public')->delete($user->img);
                }

                // Actualizar el campo img con la nueva ruta
                $user->img = $imageName;
            }

            // Guardar los cambios en el usuario
            $user->save();

            // Retornar respuesta JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Perfil actualizado correctamente.',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'img' => $user->img ? Storage::url($user->img) : null,
                    'updated_at' => $user->updated_at->format('d-m-Y'),
                    'estado' => $user->estado,
                ],
            ]);
        } catch (\Exception $e) {
            // Retornar respuesta JSON de error con detalles adicionales para depuración
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al actualizar el perfil.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request) {}
}
