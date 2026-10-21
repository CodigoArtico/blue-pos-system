<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Routing\Controller as BaseController;

class RoleController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        // Aplicar middleware para permisos
        $this->middleware('role_or_permission:ver-roles', ['only' => ['index']]);
        $this->middleware('role_or_permission:crear-roles', ['only' => ['store']]);
        $this->middleware('role_or_permission:editar-roles', ['only' => ['update', 'edit']]);
        $this->middleware('role_or_permission:eliminar-roles', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Filtrar permisos según el rol del usuario
        if ($user->hasRole('super_admin')) {
            $permisos = Permission::all(); // Mostrar todos los permisos
        } else {
            $permisos = Permission::where('name', 'not like', 'super%')->get(); // Excluir permisos que comienzan con 'super'
        }

        // Filtrar roles según el rol del usuario
        if ($user->hasRole('super_admin')) {
            $roles = Role::all(); // Mostrar todos los roles
        } else {
            $roles = Role::where('name', '!=', 'super_admin')->get(); // Excluir el rol 'super_admin'
        }

        // Retornar la vista con los roles y permisos filtrados
        return view('role.index', compact('roles', 'permisos'));
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
            // Validar entrada
            $request->validate([
                'name' => 'required|unique:roles,name',
                'permission' => 'required|array|min:1'
            ]);

            DB::beginTransaction();

            // Crear rol
            $rol = Role::create(['name' => $request->name]);

            // Asignar permisos
            $permissions = Permission::whereIn('id', $request->permission)->get();
            $rol->syncPermissions($permissions);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol registrado con éxito.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Captura de error de validación y muestra los mensajes detallados
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422); // Código 422 para error de validación
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el rol: ' . $e->getMessage(),
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
    public function edit(Role $role)
    {
        $user = Auth::user();
        $permisos = $user->hasRole('super_admin')
            ? Permission::all()
            : Permission::where('name', 'not like', 'super%')->get();

        return response()->json([
            'role' => $role,
            'permissions' => $permisos,
            'rolePermissions' => $role->permissions->pluck('id')->toArray()
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permission' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el nombre del rol
            $role->update(['name' => $request->name]);

            // Sincronizar permisos
            $permissions = Permission::whereIn('id', $request->permission)->get();
            $role->syncPermissions($permissions);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado con éxito.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors() // Asegúrate de que esto devuelve un array con 'name' y 'permission'
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Role::where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado con éxito.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }
}
