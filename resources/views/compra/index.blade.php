<x-app-layout>

    @section('title', 'compras')

    @push('css')
        @include('compra.partials.css')
        <style>
            .row-not-space {
                width: 110px;
            }

            .modal-body {
                padding: 15px;
                /* Ajusta este valor según tus necesidades */
            }

            .custom-label {
                color: black;
                margin-bottom: 0.1rem;
                /* Ajusta este valor según tus necesidades */
            }

            /* Contenedor de la tabla con scroll vertical */
            .table-scrollable {
                max-height: 300px;
                /* Ajusta la altura según tus necesidades */
                overflow-y: auto;
                /* Opcional: mantener el ancho completo */
                width: 100%;
                border: 1px solid #ddd;
                /* Opcional: agregar borde al contenedor */
            }

            /* Hacer que la cabecera sea sticky */
            .table-scrollable thead th {
                position: sticky;
                top: 0;
                background-color: #343a40;
                /* Asegúrate de que el color coincida con bg-black */
                color: #fff;
                /* Color del texto de los encabezados */
                z-index: 2;
                /* Asegura que los encabezados estén por encima del contenido */
            }

            /* Opcional: estilos adicionales para mejorar la apariencia */
            .table-scrollable tbody tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            .table-scrollable tbody tr:hover {
                background-color: #e9ecef;
            }

            /* Estilos personalizados para la tabla de detalle */
            #tabla_detalle {
                margin-top: 2px;
                /* Margen superior */
                margin-bottom: 2px;
                /* Margen inferior */
                width: 100%;
                /* Ancho total */
                border-collapse: collapse;
                /* Colapsar bordes de la tabla */
            }

            #tabla_detalle th,
            #tabla_detalle td {
                padding: 2px;
                /* Espaciado interno */
                text-align: center;
                /* Alineación central */
                border: 1px solid #ddd;
                /* Borde de celda */
            }

            #tabla_detalle th {
                background-color: #f2f2f2;
                /* Fondo de encabezado */
                color: #333;
                /* Color de texto en encabezado */
            }

            /* Estilo especial para el nombre del producto */
            .producto-nombre {
                width: 150px;
                /* Ancho personalizado */
                /* white-space: nowrap; */
                /* Evita saltos de línea */
                overflow: hidden;
                /* Oculta el contenido que excede el ancho */
                text-overflow: ellipsis;
                /* Muestra puntos suspensivos si el texto es largo */
            }
        </style>
    @endpush

    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Compras <i class="fa-solid fa-money-bill-wheat fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Compras</li>
        </ol>

        <div class="row">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('compras.index') }}" method="GET" class="row g-3">
                            <div class="col-md-5">
                                <label for="start_date" class="form-label text-dark">Fecha de inicio</label>
                                <input type="date" class="form-control shadow-sm" id="start_date" name="start_date"
                                    value="{{ $startDate }}">
                            </div>
                            <div class="col-md-5">
                                <label for="end_date" class="form-label text-dark">Fecha de fin</label>
                                <input type="date" class="form-control shadow-sm" id="end_date" name="end_date"
                                    value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 shadow-lg">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-header">
                        Monto Total de Compras
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><i class="fa-solid fa-dollar-sign fa-xl" style="color:#000000"></i>
                            {{ number_format($totalCompras, 2) }}</h5>
                        <p class="card-text">
                            Desde <span class="fw-bold"
                                style="color:#000000">{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</span>
                            Hasta <span class="fw-bold"
                                style="color:#000000">{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4 table-responsive">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-compra')
                    <div class="mb-2">
                        <button type="button" class="btn btn-dark round icon ms-4" data-bs-toggle="modal"
                            data-bs-target="#crearCompraModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                @endcan
                <div class="card-header font-semibold" style="color:#000000">
                    <i class="fas fa-table me-1"></i>
                    Tabla compras
                </div>
                <div class="d-flex ">
                    <i data-feather="download"></i>
                </div>
            </div>

            <div class="card-body px-1 pb-1 table-responsive">
                <table id="table1" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha y hora</th>
                            <th>Total</th>
                            <th>State</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $item)
                            <tr>
                                <td>
                                    <strong>
                                        <p class="mb-1" style="color:#000000">
                                            {{ $item->comprobante->tipo_comprobante }}</p>
                                    </strong>
                                    <p class="text-muted mb-0">{{ $item->numero_comprobante }}</p>
                                </td>
                                <td>
                                    @if ($item->proveedor->persona->tipo_persona == 'natural')
                                        <strong>
                                            <p class="mb-1" style="color:#000000"><i class="fa-solid fa-person"></i>
                                                {{ ucfirst($item->proveedor->persona->tipo_persona) }}
                                        </strong>
                                        </p>
                                    @else
                                        <strong>
                                            <p class="mb-1" style="color:#000000"><i
                                                    class="fa-solid fa-people-line"></i>
                                                {{ ucfirst($item->proveedor->persona->tipo_persona) }}
                                        </strong>
                                        </p>
                                    @endif
                                    <p class="text-muted mb-0"><i class="fa-solid fa-signature"
                                            style="color:#000000"></i> {{ $item->proveedor->persona->razon_social }}
                                    </p>
                                </td>
                                <td>
                                    <div class="row-not-space">
                                        <strong>
                                            <p class="mb-1"><span class="m-1"><i
                                                        class="fa-solid fa-calendar-days"></i></span>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y') }}
                                            </p>
                                            <p class="mb-0"><span class="m-1"><i
                                                        class="fa-solid fa-clock"></i></span>{{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i') }}
                                            </p>
                                        </strong>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa-solid fa-dollar-sign fa-lg" style="color:#000000"></i> <strong
                                        style="color:#000000">{{ $item->total }}</strong>
                                </td>
                                <td>
                                    @if ($item->estado == 1)
                                        <i class="fa-solid fa-circle-check fa-2xl" style="color: #16ca76;"></i>
                                    @else
                                        <i class="fa-solid fa-circle-xmark fa-2xl" style="color: #a6a6a6;"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">

                                        @can('mostrar-compra')
                                            <button type="button" class="btn icon btn" data-bs-toggle="modal"
                                                data-bs-target="#verModal-{{ $item->id }}">
                                                <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                            </button>
                                        @endcan
                                        <div class="px-1"></div>
                                        @can('eliminar-compra')
                                            @if ($item->estado == 1)
                                                <button type="button" class="btn btn icon" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal"
                                                    data-id="{{ $item->id }}"
                                                    data-comprobante="{{ $item->numero_comprobante }}">
                                                    <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @can('mostrar-compra')
            <!-- Aquí colocas los modales fuera de la tabla -->
            @foreach ($compras as $item)
                <!-- Modal vista previa -->
                <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles de la Compra</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Contenido del modal -->

                                <!-- Información de la compra -->
                                <!---Estado--->
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-bell"></i></span>
                                            <input disabled type="text" class="form-control" value="Estado: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Estado" id="icon-form" class="input-group-text"><i
                                                    class="fa-solid fa-bell"></i></span>
                                            @if ($item->estado == 1)
                                                <input disabled type="text" class="form-control" value="Activo">
                                                <span class="input-group-text"><span
                                                        class="badge bg-success">Habido</span></span>
                                            @else
                                                <input disabled type="text" class="form-control" value="Eliminado">
                                                <span class="input-group-text"><span
                                                        class="badge bg-danger">Eliminado</span></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!---Tipo comprobante--->
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-file"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="Tipo de comprobante: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Tipo de comprobante" id="icon-form" class="input-group-text"><i
                                                    class="fa-solid fa-file"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="{{ $item->comprobante->tipo_comprobante }}">
                                        </div>
                                    </div>
                                </div>
                                <!---Numero comprobante--->
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="Número de comprobante: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Número de comprobante" id="icon-form"
                                                class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="{{ $item->numero_comprobante }}">
                                        </div>
                                    </div>
                                </div>
                                <!---Proveedor--->
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-user-tie"></i></span>
                                            <input disabled type="text" class="form-control" value="Proveedor: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Proveedor" id="icon-form" class="input-group-text"><i
                                                    class="fa-solid fa-user-tie"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="{{ $item->proveedor->persona->razon_social }}">
                                        </div>
                                    </div>
                                </div>
                                <!---Fecha--->
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i
                                                    class="fa-solid fa-calendar-days"></i></span>
                                            <input disabled type="text" class="form-control" value="Fecha: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Fecha" id="icon-form" class="input-group-text"><i
                                                    class="fa-solid fa-calendar-days"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y') }}">
                                        </div>
                                    </div>
                                </div>
                                <!---Hora-->
                                <div class="row mb-4">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                                            <input disabled type="text" class="form-control" value="Hora: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Hora" id="icon-form" class="input-group-text"><i
                                                    class="fa-solid fa-clock"></i></span>
                                            <input disabled type="text" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i') }}">
                                        </div>
                                    </div>
                                </div>
                                <!---Impuesto--->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-percent"></i></span>
                                            <input disabled type="text" class="form-control" value="Impuesto: ">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <span title="Impuesto" id="icon-form" class="input-group-text"><i
                                                    class="fa-solid fa-percent"></i></span>
                                            <input disabled type="text" id="input-impuesto" class="form-control"
                                                value="{{ $item->impuesto }}">
                                        </div>
                                    </div>
                                </div>
                                <!-- Tabla de detalle de la compra -->
                                <div class="card mt-4">
                                    <div class="card-header" style="color:#000000">
                                        <i class="fas fa-table me-1"></i>
                                        Tabla de detalle de la compra
                                    </div>
                                    <div class="card-body table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-striped">
                                            <thead class="bg-dark">
                                                <tr class="align-top">
                                                    <th class="text-white">Producto</th>
                                                    <th class="text-white">Cantidad</th>
                                                    <th class="text-white">Precio de compra</th>
                                                    <th class="text-white">Precio de venta</th>
                                                    <th class="text-white">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item->productos as $producto)
                                                    <tr>
                                                        <td>{{ $producto->nombre }}</td>
                                                        <td>{{ $producto->pivot->cantidad }}</td>
                                                        <td>{{ $producto->pivot->precio_compra }}</td>
                                                        <td>{{ $producto->pivot->precio_venta }}</td>
                                                        <td>{{ $producto->pivot->cantidad * $producto->pivot->precio_compra }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot style="color:#000000">
                                                <tr>
                                                    <th colspan="5"></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4">Sumas:</th>
                                                    <th>{{ number_format($item->total, 2) }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4">IGV:</th>
                                                    <th>{{ number_format($item->impuesto, 2) }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4">Total:</th>
                                                    <th>{{ number_format($item->total + $item->impuesto, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endcan

    </div>

    @can('eliminar-compra')
        <!-- Modal de Confirmación (fuera del foreach) -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="confirmModalLabel">Mensaje de confirmación</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar el comprobante <strong id="comprobanteIdText" style="color:#000000"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('crear-compra')
        <!-- Modal para crear Compra -->
        <div class="modal fade" id="crearCompraModal" tabindex="-1" aria-labelledby="crearProductoLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Compra</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCompra" action="{{ route('compras.store') }}" method="post">
                            @csrf

                            <div class="row gy-0 mt-1">

                                <!-- Datos Generales -->
                                <div class="col-xl-4 mb-2">
                                    <div class="text-white bg-success p-1 text-center mb-1" style="border-radius: 15px;">
                                        Datos generales
                                    </div>
                                    <div class="p-3 border border-success" style="border-radius: 15px;">
                                        <div class="row">
                                            <!-- Proveedor -->
                                            <div class="col-12 mb-2">
                                                <label for="proveedore_id" class="form-label"
                                                    style="color: black;">Proveedor:</label>
                                                <select name="proveedore_id" id="proveedore_id"
                                                    class="form-control select2" title="Selecciona">
                                                    @foreach ($proveedores as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ $loop->iteration == 1 ? 'selected' : '' }}>
                                                            {{ $item->persona->razon_social }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('proveedore_id')
                                                    <small class="text-danger">{{ '*' . $message }}</small>
                                                @enderror
                                            </div>

                                            <!-- Tipo de Comprobante -->
                                            <div hidden class="col-12 mb-2">
                                                <label for="comprobante_id" class="form-label"
                                                    style="color: black;">Comprobante:</label>
                                                <select name="comprobante_id" id="comprobante_id"
                                                    class="form-control choices" title="Selecciona">
                                                    @foreach ($comprobantes as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ $loop->iteration == 3 ? 'selected' : '' }}>
                                                            {{ $item->tipo_comprobante }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('comprobante_id')
                                                    <small class="text-danger">{{ '*' . $message }}</small>
                                                @enderror
                                            </div>

                                            <!-- Número de Comprobante -->
                                            <div class="col-sm-6 mb-2">
                                                <label for="numero_comprobante" class="form-label"
                                                    style="color: black;">Num. de Comp:</label>
                                                <input readonly required type="text" name="numero_comprobante"
                                                    id="numero_comprobante" class="form-control"
                                                    value="{{ $numeroComprobante }}">
                                                @error('numero_comprobante')
                                                    <small class="text-danger">{{ '*' . $message }}</small>
                                                @enderror
                                            </div>
                                            {{-- <script>
                                            const numeroComprobante = @json($numeroComprobante);
                                            console.log(numeroComprobante);
                                        </script> --}}

                                            <!-- Impuesto -->
                                            <div class="col-sm-6 mb-2">
                                                <label for="impuesto" class="form-label"
                                                    style="color: black;">Impuesto(IGV):</label>
                                                <input readonly type="text" name="impuesto" id="impuesto"
                                                    class="form-control border-success">
                                                @error('impuesto')
                                                    <small class="text-danger">{{ '*' . $message }}</small>
                                                @enderror
                                            </div>

                                            <!-- Fecha de Emisión -->
                                            <div class="col-sm-6 mb-2">
                                                <label for="fecha" class="form-label" style="color: black;">Fecha
                                                    Emis:</label>
                                                <input readonly type="date" name="fecha" id="fecha"
                                                    class="form-control border-success" value="{{ date('Y-m-d') }}">
                                                @php
                                                    // Asegúrate de importar Carbon en el controlador o en la parte superior del archivo si estás usando nombres de espacio
                                                    $fecha_hora = \Carbon\Carbon::now()->toDateTimeString();
                                                @endphp
                                                <input type="hidden" name="fecha_hora" value="{{ $fecha_hora }}">
                                            </div>

                                            <!-- Fecha de Vencimiento -->
                                            <div class="col-sm-6">
                                                <label for="fecha_vencimiento" class="form-label"
                                                    style="color: black;">Fecha Venc:</label>
                                                <input readonly type="date" name="fecha_vencimiento"
                                                    id="fecha_vencimiento" class="form-control border-success"
                                                    value="{{ date('Y-m-d') }}">
                                            </div>

                                            <!-- Botones -->
                                            <div class="col-12 mt-4 text-center">
                                                <button type="submit" class="btn btn-success" id="guardar">Realizar
                                                    compra</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Detalles de la Compra -->
                                <div class="col-xl-8">
                                    <div class="text-white bg-dark p-1 text-center mb-1" style="border-radius: 15px;">
                                        Detalles de la compra
                                    </div>
                                    <div class="p-2 border border-dark mt-1" style="border-radius: 15px;">
                                        <div class="row">
                                            <!-- Producto -->
                                            <div class="col-12 mb-4">
                                                <select name="producto_id" id="producto_id" class="form-control select2"
                                                    title="Busque un producto aquí">
                                                    <option value="" selected disabled>Seleccione un producto</option>
                                                    @foreach ($productos as $item)
                                                        <option value="{{ $item->id }}">
                                                            {{ $item->nombre . ' - ' . $item->codigo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Cantidad -->
                                            <div class="col-sm-4 mb-2">
                                                <label for="cantidad" class="form-label"
                                                    style="color: black;">Cantidad:</label>
                                                <input type="number" name="cantidad" id="cantidad"
                                                    class="form-control">
                                            </div>

                                            <!-- Precio de Compra -->
                                            <div class="col-sm-4 mb-2">
                                                <label for="precio_compra" class="form-label"
                                                    style="color: black;">Precio de compra:</label>
                                                <input type="number" name="precio_compra" id="precio_compra"
                                                    class="form-control" step="0.1">
                                            </div>

                                            <!-- Precio de Venta -->
                                            <div class="col-sm-4 mb-2">
                                                <label for="precio_venta" class="form-label" style="color: black;">Precio
                                                    de venta:</label>
                                                <input type="number" name="precio_venta" id="precio_venta"
                                                    class="form-control" step="0.1">
                                            </div>

                                            <!-- Botón para Agregar -->
                                            <div class="col-12 mb-4 mt-2 text-end">
                                                <button id="btn_agregar" class="btn btn-primary"
                                                    type="button">Agregar</button>
                                            </div>

                                            <!-- Tabla para el Detalle de la Compra -->
                                            <div class="col-12">
                                                <div class="table-responsive table-scrollable">
                                                    <table id="tabla_detalle" class="table table-hover">
                                                        <thead class="bg-secondary">
                                                            <tr>
                                                                <th class="text-black">#</th>
                                                                <th class="text-black">Producto</th>
                                                                <th class="text-black">Cant.</th>
                                                                <th class="text-black">Pre. compra</th>
                                                                <th class="text-black">Pre. venta</th>
                                                                <th class="text-black">Subtotal</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Las filas se agregarán dinámicamente aquí -->
                                                        </tbody>
                                                        <tfoot style="color:#000000">
                                                            <tr>
                                                                <th></th>
                                                                <th colspan="4">Sumas</th>
                                                                <th colspan="2"><span id="sumas">0</span></th>
                                                            </tr>
                                                            <tr>
                                                                <th></th>
                                                                <th colspan="4">IGV %</th>
                                                                <th colspan="2"><span id="igv">0</span></th>
                                                            </tr>
                                                            <tr>
                                                                <th></th>
                                                                <th colspan="4">Total</th>
                                                                <th colspan="2">
                                                                    <input type="hidden" name="total" value="0"
                                                                        id="inputTotal">
                                                                    <span id="total">0</span>
                                                                </th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Botón para Cancelar Compra -->
                                            <div class="col-12 mt-4">
                                                <button id="cancelar" type="button" class="btn btn-danger">
                                                    Cancelar compra
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    
    @push('js')
        <script>
            $(document).ready(function() {
                $('#formCompra').submit(function(event) {
                    event.preventDefault(); // Previene el envío normal del formulario
                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#crearCompraModal').modal('hide'); // Cerrar el modal
                                reproducirSonidoSuccess();
                                mostrarToast('success', response.message);
                                setTimeout(() => location.reload(),
                                    1500); // Recargar la página después de 1.5 segundos
                            }
                        },
                        error: function(xhr) {
                            const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                                .responseJSON.message : 'Error al realizar la compra';
                            reproducirSonidoAlert();
                            mostrarToast('error', errorMsg, 'error')
                        },
                        complete: function() {
                            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                            hideSpinner();
                        }
                    });
                });
                $('#btn_agregar').click(function() {
                    agregarProducto();
                });

                // Evento para cancelar compra con SweetAlert2
                $('#cancelar').click(function() {
                    confirmarCancelacion();
                });

                disableButtons();

                $('#impuesto').val(impuesto);
            });

            // Inicializar Select2 para el campo de producto en el modal
            $(document).ready(function() {
                $('#producto_id').select2({
                    placeholder: "Selecciona un producto",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#crearCompraModal') // El modal al que pertenece el select
                });

                $('#proveedore_id').select2({
                    placeholder: "Selecciona un proveedor",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#crearCompraModal') // El modal al que pertenece el select
                });
                
            });

            //Variables
            let cont = 0;
            let sumas = 0;
            let igv = 0;
            let total = 0;

            //Constantes
            // const impuesto = 18;
            const impuesto = 0;

            function confirmarCancelacion() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Seguro que quieres cancelar la compra? Todos los datos se perderán.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No, mantener'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Ejecutar la función de cancelar compra
                        cancelarCompra();
                        Swal.fire(
                            'Cancelada',
                            'La compra ha sido cancelada.',
                            'success'
                        );
                    }
                });
            }

            function cancelarCompra() {
                // Eliminar el tbody de la tabla
                document.activeElement.blur(); // Elimina el foco del botón dentro del modal
                $('#crearCompraModal').modal('hide');

                // Reiniciar valores de las variables
                cont = 0;
                sumas = 0;
                igv = 0;
                total = 0;

                // Mostrar los campos calculados
                $('#sumas').html(sumas.toFixed(2));
                $('#igv').html(igv.toFixed(2));
                $('#total').html(total.toFixed(2));
                $('#impuesto').val(impuesto + '%');
                $('#inputTotal').val(total.toFixed(2));

                limpiarCampos();
                disableButtons();
            }

            function disableButtons() {
                if (total === 0) {
                    $('#guardar').hide();
                    $('#cancelar').hide();
                } else {
                    $('#guardar').show();
                    $('#cancelar').show();
                }
            }

            function agregarProducto() {
                // Obtener valores de los campos
                let idProducto = $('#producto_id').val();
                // Extraer el nombre del producto correctamente
                let nameProducto = ($('#producto_id option:selected').text()).split(' - ')[0].trim();
                let cantidad = parseInt($('#cantidad').val());
                let precioCompra = parseFloat($('#precio_compra').val());
                let precioVenta = parseFloat($('#precio_venta').val());

                // Validaciones 
                if (nameProducto && cantidad && precioCompra && precioVenta) {
                    if (cantidad > 0 && Number.isInteger(cantidad) && precioCompra > 0 && precioVenta > 0) {
                        if (precioVenta > precioCompra) {

                            // Verificar si el producto ya existe en la tabla
                            let productoExiste = false;
                            $('#tabla_detalle tbody tr').each(function() {
                                let idFila = $(this).find('input[name="arrayidproducto[]"]').val();
                                if (idFila === idProducto) {
                                    productoExiste = true;
                                    return false; // Salir del each
                                }
                            });

                            if (productoExiste) {
                                reproducirSonidoAlert();
                                showModalInfo('El producto ya está en la tabla.');
                                return; // No agregar el producto
                            }

                            // Calcular subtotal
                            let subtotal = round(cantidad * precioCompra);

                            // Crear la fila
                            let fila = '<tr id="fila' + cont + '">' +
                                '<th>' + (cont + 1) + '</th>' +
                                '<td><input type="hidden" name="arrayidproducto[]" value="' + idProducto + '">' + nameProducto +
                                '</td>' +
                                '<td><input type="hidden" name="arraycantidad[]" value="' + cantidad + '">' + cantidad +
                                '</td>' +
                                '<td><input type="hidden" name="arraypreciocompra[]" value="' + precioCompra.toFixed(2) + '">' +
                                precioCompra.toFixed(2) + '</td>' +
                                '<td><input type="hidden" name="arrayprecioventa[]" value="' + precioVenta.toFixed(2) + '">' +
                                precioVenta.toFixed(2) + '</td>' +
                                '<td class="subtotal">' + subtotal.toFixed(2) + '</td>' +
                                '<td><button class="btn btn icon" type="button" onClick="eliminarProducto(' + cont +
                                ')"><i class="fa-solid fa-rectangle-xmark" style="color: #bd001c;"></i></button></td>' +
                                '</tr>';

                            // Acciones después de añadir la fila
                            $('#tabla_detalle tbody').append(fila);
                            limpiarCampos();
                            cont++;

                            // Actualizar los cálculos
                            actualizarTotales();
                            disableButtons();


                        } else {
                            reproducirSonidoAlert();
                            showModal('Precio de venta debe ser mayor que el precio de compra.');
                        }

                    } else {
                        reproducirSonidoAlert();
                        showModal('Valores de cantidad o precios incorrectos.');
                    }

                } else {
                    reproducirSonidoAlert();
                    showModal('Le faltan campos por llenar.');
                }
            }

            function eliminarProducto(indice) {
                // Eliminar la fila de la tabla
                $('#fila' + indice).remove();

                // Reorganizar índices de filas después de eliminar
                $('#tabla_detalle tbody tr').each(function(index) {
                    $(this).find('th:first').text(index + 1); // Actualizar número de fila
                    $(this).attr('id', 'fila' + index); // Actualizar ID de la fila

                    // Actualizar los nombres e identificadores de los inputs y botones
                    $(this).find('input').each(function() {
                        let name = $(this).attr('name');
                        // No es necesario actualizar el nombre de los inputs si no dependen del índice
                    });
                    $(this).find('button').attr('onClick', 'eliminarProducto(' + index + ')');
                });

                // Decrementar el contador
                cont = $('#tabla_detalle tbody tr').length;

                // Actualizar los cálculos
                actualizarTotales();
                disableButtons();
            }

            function limpiarCampos() {
                let select = $('#producto_id');
                select.val(null).trigger('change'); // Establece el valor del select a la opción por defecto
                $('#cantidad').val('');
                $('#precio_compra').val('');
                $('#precio_venta').val('');
            }

            function round(num, decimales = 2) {
                return Number(Math.round(num + 'e' + decimales) + 'e-' + decimales);
            }

            function showModal(message, icon = 'error') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: icon,
                    title: message
                });
            }

            function showModalInfo(message, icon = 'info') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: icon,
                    title: message
                });
            }

            function actualizarTotales() {
                sumas = 0;
                // Iterar sobre cada subtotal en la tabla para calcular sumas
                $('#tabla_detalle tbody tr').each(function() {
                    let subtotal = parseFloat($(this).find('.subtotal').text()) || 0;
                    sumas += subtotal;
                });

                igv = round(sumas * (impuesto / 100));
                total = round(sumas + igv);

                // Mostrar los campos calculados
                $('#sumas').html(sumas.toFixed(2));
                $('#igv').html(igv.toFixed(2));
                $('#total').html(total.toFixed(2));
                $('#impuesto').val(impuesto);
                $('#inputTotal').val(total.toFixed(2));
            }

            // Configurar CSRF Token para todas las solicitudes AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Manejar el clic en el botón de confirmar eliminación dentro del modal
            $(document).ready(function() {
                let compraIdToDelete = null;

                // Manejar la apertura del modal
                $('#table1').on('click', 'button[data-bs-target="#confirmModal"]', function() {
                    compraIdToDelete = $(this).data('id'); // Obtener el ID de la compra
                    compraComprobante = $(this).data('comprobante');
                    // console.log(compraComprobante);
                    // Rellenar el modal con el ID del comprobante
                    $('#comprobanteIdText').text(compraComprobante);
                });

                // Manejar la acción de eliminar
                $('#confirmDeleteButton').on('click', function() {
                    if (!compraIdToDelete) {
                        mostrarToast('error', 'ID de compra no encontrado.');
                        return;
                    }

                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();

                    // Realizar la solicitud AJAX para eliminar
                    $.ajax({
                        url: "{{ url('/compras') }}/" + compraIdToDelete,
                        type: 'DELETE',
                        success: function(response) {
                            $('#confirmModal').modal('hide'); // Cerrar el modal
                            reproducirSonidoSuccess();
                            mostrarToast('success', response.message);
                            setTimeout(() => location.reload(), 1500); // Recargar la página después de 1.5 segundos
                        },
                        error: function(xhr) {
                            let errorMsg = 'Ocurrió un error al eliminar la compra.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            mostrarToast('error', errorMsg);
                        },
                        complete: function() {
                            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                            hideSpinner();
                            compraIdToDelete = null; // Resetear el ID después de la acción
                        }
                    });
                });
            });
        </script>
    @endpush

</x-app-layout>
