<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // dashboard y charts
            'ver-panel-principal',
            // 'ver-charts-secundarios',
            
            // Perfil 
            'ver-perfil',
            'editar-perfil',
            
            //categorías
            'ver-categoria',
            'crear-categoria',
            'editar-categoria',
            'eliminar-categoria',
            
            // Marca
            'ver-marca',
            'crear-marca',
            'editar-marca',
            'eliminar-marca',

            // Presentacione
            'ver-presentacion',
            'crear-presentacion',
            'editar-presentacion',
            'eliminar-presentacion',

            // Producto
            'ver-producto',
            'crear-producto',
            'editar-producto',
            'eliminar-producto',

            // Venta
            'ver-ventas',
            'crear-venta',
            'mostrar-venta',
            'eliminar-venta',
            'modificar-fecha-venta',

            // Cliente
            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'eliminar-cliente',

            // Compra
            'ver-compras',
            'crear-compra',
            'mostrar-compra',
            'eliminar-compra',

            // Proveedor
            'ver-proveedor',
            'crear-proveedor',
            'editar-proveedor',
            'eliminar-proveedor',

            // Empresa
            'ver-empresa',
            'configuracion-datos-empresa',

            // User
            'ver-users',
            'crear-user',
            'editar-user',
            'eliminar-user',

            // Roles
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'eliminar-roles',

            // Informes
            'informes',

            // Gastos y Caja
            'ver-gastos',
            'crear-gastos',
            'editar-gastos',
            'eliminar-gastos',
            'ver-caja',

            // Billboard 
            'ver-billboard',
            'editar-billboard',
            'mostrar-billboard',

            // Mesas
            'ver-mesas',
            'detalle-mesas',

            //Pedidos
            'admin-pedidos',

            // ADMINIDTRADOR
            'ver-sesiones', // ver sesiones del usuario
            'ver-manual-user', //manual del usuario
            
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
    }
}
