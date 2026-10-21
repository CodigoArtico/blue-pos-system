<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-users', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-user', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-user', ['only' => ['update', 'edit']]);
        $this->middleware('role_or_permission:eliminar-user', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $currentUser = auth()->user();

        if ($currentUser->hasRole('super_admin')) {
            // Si es super_admin, obtener todos los usuarios activos
            $users = User::where('estado', '1')->get();

            // Si es super_admin, obtener todos los usuarios inactivos
            $usersDelete = User::where('estado', '0')->get();

            // Obtener todos los roles
            $roles = Role::all();
        } else {
            // Si no es super_admin, obtener solo usuarios activos que no tienen el rol super_admin
            $users = User::where('estado', '1')
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'super_admin');
                })
                ->get();

            // Si no es super_admin, obtener solo usuarios inactivos que no tienen el rol super_admin
            $usersDelete = User::where('estado', '0')
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'super_admin');
                })
                ->get();

            // Obtener todos los roles excepto super_admin
            $roles = Role::where('name', '!=', 'super_admin')->get();
        }

        // Obtener todas las sesiones agrupadas por user_id
        $sessions = DB::table('sessions')->get()->groupBy('user_id');

        // Pasar los usuarios, las sesiones y los roles a la vista
        return view('user.index', compact('users', 'usersDelete', 'sessions', 'roles'));
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
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

            // Encriptar la contraseña y crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Asignar el rol
            $user->assignRole($request->role);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado con éxito.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario: ' . $e->getMessage()
            ], 500);
        }
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
    public function edit(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first()
            ]
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            // Comprobar y aplicar el Hash al password si está presente
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            // Actualizar rol
            $user->syncRoles([$request->role]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado con éxito.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            DB::transaction(function () use ($user) {
                // Eliminar roles del usuario si existen
                if ($user->roles()->exists()) {
                    $user->syncRoles([]);
                }

                // Cambiar estado del usuario a "inactivo"
                $user->estado = '0';
                // Generar una contraseña aleatoria e inservible
                $user->password = bcrypt(Str::random(40)); 
                $user->save();

                // Invalidar todas las sesiones del usuario
                DB::table('sessions')->where('user_id', $user->id)->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del usuario: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroysesion($sessionId)
    {
        // Eliminar la sesión de la base de datos
        $deleted = DB::table('sessions')->where('id', $sessionId)->delete();

        if ($deleted) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false], 404);
        }
    }
    public function destroysesionAll($userId)
    {
        try {
            // Eliminar todas las sesiones del usuario
            $deleted = DB::table('sessions')->where('user_id', $userId)->delete();

            // Verificar si se eliminaron sesiones
            if ($deleted) {
                return response()->json(['message' => 'Todas las sesiones cerradas exitosamente.'], 200);
            } else {
                return response()->json(['message' => 'No se encontraron sesiones para cerrar.'], 404);
            }
        } catch (Exception $e) {
            // Manejo de errores
            return response()->json(['message' => 'Error al cerrar las sesiones: ' . $e->getMessage()], 500);
        }
    }
}
