<?php

use App\Http\Controllers\BillboardController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MesasController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PresentacioneController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SIREController;
use App\Http\Controllers\TicketVentaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VerificarBajaController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    // Aquí puedes agregar más rutas que necesiten los mismos middleware

    Route::resources([
        'categorias' => CategoriaController::class,
        'marcas' => MarcaController::class,
        'presentaciones' => PresentacioneController::class,
        'productos' => ProductoController::class,
        'clientes' => ClienteController::class,
        'proveedores' => ProveedorController::class,
        'compras' => CompraController::class,
        'ventas' =>  VentaController::class,
        'users' => UserController::class,
        'roles' => RoleController::class,
        'manual' => ManualController::class,
        'empresas' => EmpresaController::class,

        'informe' => InformeController::class,
        'caja' => CajaController::class,
        'gastos' => GastoController::class,

        'billboard' => BillboardController::class,
        'mesas' => MesasController::class,
    ]);

    //--------------------------------------VERIFICAR LAS BAJAS DE COMPROBANTES------------------------------------------------------------------
    Route::get('/run-verificar-baja', [VerificarBajaController::class, 'runCommand']);

    //--------------------------------------CONSULTAS DNI, RUC Y GUARDAR CLIENTE O PROVEEDOR-----------------------------------------------------
    Route::post('/consulta/dni', [DocumentController::class, 'getDni']);
    Route::post('/consulta/ruc', [DocumentController::class, 'getRuc']);
    Route::post('/guardar-cliente', [DocumentController::class, 'guardarCliente'])->name('guardar.cliente');
    Route::post('/guardar-proveedor', [DocumentController::class, 'guardarProveedor'])->name('guardar.proveedor');

    //---------------------------------------------------------INFORME - EXCEL Y PDF DE PRODUCTOS----------------------------------------------------------
    Route::get('/export', [InformeController::class, 'export'])->name('export');
    Route::get('/export-pdf', [InformeController::class, 'export_pdf'])->name('export-pdf');

    //-------------------------------------------------PEDIDOS USUARIOS WEB------------------------------------------------------------------------------
    Route::get('/ordenes', [OrdenController::class, 'index'])->name('orden.index');
    Route::get('/ordenes/{id}', [OrdenController::class, 'getOrderDetails']); //obtener una orden especifico
    Route::get('/ordenes/{orderId}/venta/{identificadorVenta}', [OrdenController::class, 'mostrarVenta']); //obtener una orden vendido con detalle.

    // --------------------------------------------------------------------ORDENES-------------------------------------------------------------------------
    Route::post('/ordenes/{id}/cambiar-estado', [OrdenController::class, 'cambiarEstado'])->name('ordenes.cambiarEstado'); //CAMBIAR ESTADO DE ORDENES
    Route::get('/ventas/create', [VentaController::class, 'create'])->name('ventas.create'); //VENTAS DE ORDENES
    Route::post('/procesar-orden', [VentaController::class, 'procesarOrden'])->name('procesar.orden'); //PROCESAR ORDENES PARA VENDER

    //---------------------------------------------------------------MESAS---------------------------------------------------------------------------------
    Route::get('/ticket-mesa/{id}', [MesasController::class, 'generarTicket']); //VER TICKET DE LA MESA OCUPADA
    Route::post('/mesas/{id}/add-product', [MesasController::class, 'addProduct'])->name('mesas.addProduct'); //AÑADIR PRODUCTO A LA MESA
    Route::delete('/mesas/{mesa}/productos/{producto}', [MesasController::class, 'removeProduct'])->name('mesas.removeProduct'); //QUITAR PRODUCTOS DE LA MESA
    Route::put('/mesas/{mesa}/productos/{producto}', [MesasController::class, 'updateProduct'])->name('mesas.updateProduct'); //ACTUALIZAR CANTIDAD DE LA MESA
    Route::post('/venta/storemesa', [VentaController::class, 'storemesa'])->name('venta.storemesa'); //VENDER TODOS LOS PRODUCTOS ASIGNADOS A LA MESA.

    // ---------------------------------MOSTRAR PERFIL Y ACTUAIZAR DATOS Y CONTRASEÑA----------------------------------------------------------
    Route::get('/perfil', [ProfileController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('perfil.update');

    //------------------------------------CERRAR SESIÓN ESPECIFICO Y TODOS------------------------------------------------------------------------
    Route::delete('/sessions/{sessionId}', [UserController::class, 'destroysesion'])->name('sessions.destroy');
    Route::delete('/users/{user}/sessions', [UserController::class, 'destroysesionAll'])->name('sessions.destroyAll');
});

// ----------------------------------------------VENTA: TICKET | FORMATO A4------------------------------------------------------------------
Route::get('/venta/{identificador_web}/ticket', [TicketVentaController::class, 'abrirVentanaPDFTicket'])->name('venta.abrirVentanaPDFTicket');
Route::get('/venta/{identificador_web}/ticket-a4', [TicketVentaController::class, 'abrirVentanaPDFA4'])->name('venta.abrirVentanaPDFA4');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/contar-ordenes', [OrdenController::class, 'contarOrdenes'])->name('contar.ordenes'); //CONTAR ORDENES DE CAMPANA
});



require __DIR__ . '/auth.php';
