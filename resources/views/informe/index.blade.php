<x-app-layout>

    @section('title', ' Informe Productos')

    @push('css')
        {{-- estilos para los modales de estadistica clientes y usuarios  --}}
        <style>
            .scroll-container {
                width: 100%;
                overflow-x: auto;
                /* Permite el scroll horizontal */
                -webkit-overflow-scrolling: touch;
                /* Mejora la experiencia en dispositivos táctiles */
            }

            .list-group {
                display: flex;
                /* Disposición en fila */
                flex-wrap: nowrap;
                /* No permitir el salto de línea */
                margin: 0;
                padding: 0;
                list-style-type: none;
            }

            .list-group-item {
                min-width: 200px;
                /* Establecer un tamaño mínimo para cada elemento de la lista */
                margin-right: 10px;
                /* Espaciado entre elementos */
                white-space: normal;
                /* Permite que el texto se ajuste correctamente */
            }
        </style>

        <!-- Estilos de DataTables -->
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

        <!-- Scripts de jQuery y DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <!-- Estilos para el gráfico -->
        <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    @endpush
    <script>
        function exportData(type) {
            // Obtener el spinner desde el DOM
            const spinner = document.getElementById('loading-screen');

            // Verificar si el spinner existe antes de modificarlo
            if (spinner) {
                // Mostrar el spinner antes de iniciar la descarga
                spinner.style.display = 'flex';
            }

            // Obtener las fechas seleccionadas del formulario
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Definir la URL de exportación dependiendo del tipo (excel o pdf)
            let url = '';

            if (type === 'excel') {
                url = `{{ route('export') }}?start_date=${startDate}&end_date=${endDate}`;
            } else if (type === 'pdf') {
                url = `{{ route('export-pdf') }}?start_date=${startDate}&end_date=${endDate}`;
            }

            // Si es PDF, abrir en una nueva ventana (pestaña)
            if (type === 'pdf') {
                window.open(url, '_blank'); // Esto abrirá el PDF en una nueva pestaña
            } else {
                // Para otros tipos, redirigir a la URL después de un pequeño retraso
                setTimeout(function() {
                    window.location.href = url; // Redirigir a la URL para Excel
                }, 500); // 500ms de retraso
            }

            // Ocultar el spinner después de un pequeño retraso (cuando la descarga se ha iniciado)
            if (spinner) {
                setTimeout(function() {
                    spinner.style.display = 'none'; // Ocultar el spinner
                }, 500); // Mantener el spinner visible por 500ms antes de ocultarlo
            }
        }
    </script>

    <div class="main-content container-fluid">
        <div class="page-title">
            <h1 class="mt-4 text-center text-dark">Productos en Informes <i
                    class="fa-solid fa-triangle-exclamation fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Producto</li>
                        <li class="breadcrumb-item active" aria-current="page">Productos Informes</li>
                    </ol>
                </nav>
            </div>

        </div>
        <br>
        {{-- FILTRADO DE FECHAS --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('informe.index') }}" method="GET" class="row g-3">
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
        </div>

        {{-- INFORME PRODUCTOS  --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-header text-dark">
                    <i class="fas fa-table me-1"></i>
                    Informe de Productos, Ventas y Márgenes
                </div>
                <div class="d-flex">
                    <a href="#" onclick="exportData('excel')" class="btn btn-success icon">
                        <i class="fa-solid fa-file-excel"></i> <i data-feather="download"></i>
                    </a>
                    <div class="px-1"></div>
                    <a href="#" onclick="exportData('pdf')" class="btn btn-danger icon">
                        <i class="fa-solid fa-file-pdf"></i> <i data-feather="download"></i>
                    </a>
                </div>
            </div>
            <div class="card-body px-0 pb-0">
                <div class="table-responsive mx-3 mb-2">
                    <table class='table table-hover table-striped table-bordered table-light mb-0' id="productosTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Marca</th>
                                <th>Presentación</th>
                                <th>Categorías</th>
                                <th>Cant. Venta</th>
                                <th>Total de Ventas</th>
                                <th>Precio de Compra</th>
                                <th>Precio de Venta</th>
                                <th>Margen de Ganancia</th>
                                <th>Porcentaje de Ganancia (%)</th>
                                <th>Stock Disponible</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $item)
                                <tr>
                                    <td>{{ $item->codigo }}</td>
                                    <td>
                                        @if ($item->stock >= 1 && $item->stock <= 5 && $item->estado == 1)
                                            {{ $item->nombre }} <span
                                                class="badge bg-warning badge-pill badge-round ml-1">{{ $item->stock }}</span>
                                        @elseif ($item->stock == 0 && $item->estado == 1)
                                            {{ $item->nombre }} <span
                                                class="badge bg-danger badge-pill badge-round ml-1">{{ $item->stock }}</span>
                                        @else
                                            {{ $item->nombre }} <span
                                                class="badge bg-primary badge-pill badge-round ml-1">{{ $item->stock }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->marca->caracteristica->nombre }}</td>
                                    <td>{{ $item->presentacion->caracteristica->nombre }}</td>
                                    <td>
                                        @foreach ($item->categorias as $category)
                                            <span
                                                class="badge bg-secondary text-white">{{ $category->caracteristica->nombre }}</span>
                                        @endforeach
                                    </td>
                                    <td><span class="badge bg-info">{{ $ventas_por_producto[$item->id] }}</span></td>
                                    <td>S/. {{ number_format($ventas_totales_por_producto[$item->id], 2) }}</td>
                                    <td>S/. {{ number_format($item->precioCompraReciente(), 2) }}</td>
                                    <td>S/. {{ number_format($item->precioVentaReciente(), 2) }}</td>
                                    <td>S/. {{ number_format($margen_por_producto[$item->id], 2) }}</td>
                                    <td>{{ number_format($porcentaje_ganancia_por_producto[$item->id], 2) }}%</td>
                                    <td>{{ $stock_por_producto[$item->id] }}</td>
                                    <td>
                                        @if ($item->estado == 1)
                                            <span class="badge bg-success"><i
                                                    class="fa-solid fa-circle-check"></i></span>
                                        @else
                                            <span class="badge bg-light"><i class="fa-solid fa-circle-xmark"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('ver-producto')
                                                <button type="button" class="btn icon btn" data-bs-toggle="modal"
                                                    data-bs-target="#verModal-{{ $item->id }}">
                                                    <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal vista previa -->
                                <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles del
                                                    producto</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-dark">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p><span class="fw-bolder">Descripción:</span>
                                                            {{ $item->descripcion }}</p>
                                                    </div>
                                                    <div class="col-12">
                                                        <p><span class="fw-bolder">Stock:</span>
                                                            @if ($item->stock >= 1 && $item->stock <= 5 && $item->estado == 1)
                                                                <span
                                                                    class="badge bg-warning badge-pill badge-round ml-1">{{ $item->stock }}</span>
                                                            @elseif ($item->stock == 0 && $item->estado == 1)
                                                                <span
                                                                    class="badge bg-danger badge-pill badge-round ml-1">{{ $item->stock }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-primary badge-pill badge-round ml-1">{{ $item->stock }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-12">
                                                        <p><span class="fw-bolder">Precio:</span> S/.
                                                            {{ number_format($item->precioVentaReciente(), 2) }}</p>
                                                    </div>
                                                    <div class="col-12">
                                                        <p class="fw-bolder">Imagen Subida:</p>
                                                        <div class="text-center">
                                                            @if ($item->img_path)
                                                                <img loading="lazy" rel="preload"
                                                                    src="{{ Storage::url('public/productos/' . $item->img_path) }}"
                                                                    alt="{{ $item->nombre }}"
                                                                    class="img-fluid img-thumbnail"
                                                                    style="width: 150px; height: 150px;">
                                                            @endif
                                                        </div>
                                                        <p class="fw-bolder">Imagen Url:</p>
                                                        <div class="text-center">
                                                            @if ($item->img_url)
                                                                <img loading="lazy" rel="preload"
                                                                    src="{{ $item->img_url }}"
                                                                    alt="{{ $item->nombre }}"
                                                                    style="width: 150px; height: 150px;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Ganancia Total y Ventas en un gráfico circular --}}
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title text-dark">Ganancia y Ventas</h5>
                        <canvas id="gananciaVentasChart" width="100" height="100"></canvas>
                        <p class="card-text text-success">
                            {{-- Inicializar variables --}}
                            @php
                                $totalGanancia = 0;
                                $ventasTotalesEliminadas = 0;
                                $totalVentas = 0;

                                foreach ($productos as $producto) {
                                    $compra_producto = \App\Models\CompraProducto::where('producto_id', $producto->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                    $precio_compra = $compra_producto ? $compra_producto->precio_compra : 0;
                                    $precio_venta = $producto->precioVentaReciente();

                                    $ventas = \App\Models\Venta::whereHas('productos', function ($query) use (
                                        $producto,
                                    ) {
                                        $query->where('producto_id', $producto->id);
                                    })
                                        ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                        ->get();

                                    foreach ($ventas as $venta) {
                                        $cantidad =
                                            $venta->productos->where('id', $producto->id)->first()->pivot->cantidad ??
                                            0;

                                        if ($venta->estado == 1) {
                                            // Solo calcula ganancia y ventas totales para estado 1
                                            $totalGanancia += ($precio_venta - $precio_compra) * $cantidad;
                                            $totalVentas += $cantidad * $precio_venta;
                                        }

                                        if ($venta->estado == 0) {
                                            // Acumula el monto total de ventas eliminadas
                                            $ventasTotalesEliminadas += $cantidad * $precio_venta;
                                        }
                                    }

                                    // Sumar monto total de productos libres
                                    $productosLibres = \App\Models\Producto_Libre::whereBetween('created_at', [
                                        $startDate . ' 00:00:00',
                                        $endDate . ' 23:59:59',
                                    ])->get();
                                }

                                $gananciaFinal = $totalGanancia; // Ya solo filtra ventas con estado 1
                                $montoVentaTotal = $totalVentas;

                            @endphp

                            {{-- <strong>S/. {{ number_format($gananciaFinal, 2) }}</strong> --}}
                        </p>
                        {{-- <p class="card-text text-dark">Total de Ventas: <strong>S/.
                                {{ number_format($totalVentas, 2) }}</strong></p> --}}
                    </div>
                </div>
            </div>
            @include('informe.partials.ganancias-ventas')
            {{-- Gráfico circular para métodos de pago --}}
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title text-dark">Métodos de Pago</h5>
                        <canvas id="metodosPagoChart" width="100" height="100"></canvas>
                    </div>
                </div>
            </div>
            @include('informe.partials.metodos-pagos')
            {{-- Gráfico circular para Ventas por Comprobantes --}}
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title text-dark">Ventas por Comprobantes</h5>
                        <canvas id="ventasComprobantesChart" width="100" height="100"></canvas>
                    </div>
                </div>
            </div>
            @include('informe.partials.ventas-por-comprobante')
            {{-- Gráfico circular para Ventas Productos Libres --}}
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title text-dark">Ventas de Productos Libres</h5>
                        <canvas id="productosLibresChart" width="100" height="100"></canvas>
                    </div>
                </div>
            </div>
            @include('informe.partials.ventas-productos-libres')

        </div>

        {{-- LISTA DE PRODUCTOS VENTAS  --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-header text-dark">
                    <i class="fas fa-table me-1"></i>
                    Lista de Productos Vendidos
                </div>
            </div>
            <div class="card-body px-0 pb-0">
                <div class="table-responsive mx-3 mb-2">
                    <table class='table table-hover table-striped table-bordered table-light mb-0' id="ventasTable">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio de Venta</th>
                                <th>Ganancia</th>
                                <th>Porcentaje de Ganancia</th>
                                <th>Total Venta</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th> <!-- Nueva columna Estado -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productoVentas as $productoVenta)
                                @php
                                    // Obtener el precio de compra más reciente
                                    $precio_compra = $productoVenta->producto->precioCompraReciente();

                                    // Obtener el precio de venta
                                    $precio_venta = $productoVenta->producto->precioVentaReciente();

                                    // Calcular la ganancia
                                    $ganancia = $precio_venta - $precio_compra;

                                    // Calcular el porcentaje de ganancia
                                    $porcentaje_ganancia = $precio_compra > 0 ? ($ganancia / $precio_compra) * 100 : 0;

                                    // Calcular el total de la venta
                                    $total_venta = $productoVenta->cantidad * $precio_venta;

                                    // Obtener el estado de la venta
                                    $estado_venta = $productoVenta->venta->estado == 1 ? 'Activo' : 'Anulado';
                                @endphp
                                <tr>
                                    <td>{{ $productoVenta->id }}</td>
                                    <td>{{ $productoVenta->producto->nombre }}</td>
                                    <td>{{ $productoVenta->cantidad }}</td>
                                    <td>S/. {{ number_format($precio_venta, 2) }}</td>
                                    <td>S/. {{ number_format($ganancia * $productoVenta->cantidad, 2) }}</td>
                                    <td>{{ number_format($porcentaje_ganancia, 2) }}%</td>
                                    <td>S/. {{ number_format($total_venta, 2) }}</td>
                                    <td>{{ $productoVenta->venta->user->name }}</td>
                                    <td>{{ $productoVenta->created_at }}</td>
                                    <td>
                                        <!-- Mostrar el estado como un badge -->
                                        @if ($estado_venta == 'Activo')
                                            <span class="badge bg-success">Emitido</span>
                                        @else
                                            <span class="badge bg-light">{{ $estado_venta }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        {{-- GRAFICOS --}}
        <div class="row">
            <!-- Gráfico de estadísticas de usuarios -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header text-dark d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-chart-bar me-1"></i>
                            Gráfico de Ventas por Usuario
                        </div>
                        <button class="btn btn-link icon" id="expandUserBtn" data-bs-toggle="modal"
                            data-bs-target="#expandUserModal">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="usuariosChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Modal de estadísticas de usuarios -->
            <div class="modal fade" id="expandUserModal" tabindex="-1" aria-labelledby="expandUserModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <!-- Cabecera con título y botón de cierre -->
                                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-list me-1"></i>
                                        Estadísticas de Usuarios
                                    </div>
                                    <!-- Botón de cerrar (X) -->
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <!-- Cuerpo del modal -->
                                <div class="card-body">
                                    <div class="scroll-container">
                                        <ul class="list-group">
                                            @foreach ($userNames as $index => $userName)
                                                <li class="list-group-item">
                                                    <strong>{{ $userName }}</strong><br>
                                                    Ventas: {{ $userTotalVentas[$index] }}<br>
                                                    Monto Total Ventas:
                                                    S/.{{ number_format($userMontoVentas[$index], 2) }}<br>
                                                    Puntaje: {{ number_format($userPuntos[$index], 2) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- script para grafico usuario  --}}
            @include('informe.partials.scripts-graficos-user')

            <!-- Gráfico de estadísticas de clientes -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header text-dark d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-chart-bar me-1"></i>
                            Gráfico de Ventas por Cliente
                        </div>
                        <!-- Botón de abrir modal (expansión) alineado a la derecha -->
                        <button class="btn btn-link icon ms-auto" id="expandClientModalLabel" data-bs-toggle="modal"
                            data-bs-target="#expandClientModal">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="clientesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Modal Estadísticas y puntajes de clientes -->
            <div class="modal fade" id="expandClientModal" tabindex="-1" aria-labelledby="expandClientModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <!-- Cabecera con título y botón de cierre -->
                                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-list me-1"></i>
                                        Estadísticas de los Mejores Clientes
                                    </div>
                                    <!-- Botón de cerrar (X) -->
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <!-- Cuerpo del modal -->
                                <div class="card-body">
                                    <div class="scroll-container">
                                        <ul class="list-group">
                                            @foreach ($clientNames as $index => $clientName)
                                                <li class="list-group-item">
                                                    <strong>{{ $clientName }}</strong><br>
                                                    Ventas: {{ $clientTotalVentas[$index] }}<br>
                                                    Monto Total Ventas: S/.
                                                    {{ number_format($clientMontoVentas[$index], 2) }}<br>
                                                    Puntaje: {{ number_format($clientPuntos[$index], 2) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- script para grafico clientes  --}}
            @include('informe.partials.scripts-graficos-clientes')
        </div>

        {{-- PRODUCTOS LIBRES  --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-header text-dark">
                    <i class="fas fa-table me-1"></i>
                    Lista de Productos Libres Vendidos
                </div>
            </div>
            <div class="card-body px-0 pb-0">
                <div class="table-responsive mx-3 mb-2">
                    <table class='table table-hover table-striped table-bordered table-light mb-0'
                        id="productosLibresTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio de Venta</th>
                                <th>Descuento</th>
                                <th>Total Venta</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th> <!-- Nueva columna Estado -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productoLibres as $productoLibre)
                                @php
                                    // Verificar si la relación 'venta' y 'user' están presentes
                                    $venta = $productoLibre->venta;
                                    $user = $venta ? $venta->user : null;
                                    $userName = $user ? $user->name : 'Sin usuario';
                                @endphp
                                <tr>
                                    <td>{{ $productoLibre->id }}</td>
                                    <td>{{ $productoLibre->nombre }}</td>
                                    <td>{{ $productoLibre->cantidad }}</td>
                                    <td>S/. {{ number_format($productoLibre->precio_venta, 2) }}</td>
                                    <td>S/. {{ number_format($productoLibre->descuento, 2) }}</td>
                                    <td>S/.
                                        {{ number_format($productoLibre->cantidad * $productoLibre->precio_venta, 2) }}
                                    </td>
                                    <td>{{ $userName }}</td> <!-- Mostrar el nombre del usuario si existe -->
                                    <td>{{ $productoLibre->venta ? $productoLibre->venta->created_at : 'No disponible' }}
                                    </td>

                                    <td>
                                        <!-- Mostrar el estado de la venta -->
                                        @if ($productoLibre->venta && $productoLibre->venta->estado == 1)
                                            <span class="badge bg-success">Emitido</span>
                                        @else
                                            <span class="badge bg-light">Anulado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                $('#productosTable').DataTable({
                    "paging": true, // Habilitar paginación
                    "searching": true, // Habilitar búsqueda
                    "ordering": true, // Habilitar ordenación de columnas
                    "info": true, // Mostrar información de la tabla
                    "lengthChange": true // Permitir cambiar el número de filas por página
                });

                $('#ventasTable').DataTable({
                    "paging": true, // Habilitar paginación
                    "searching": true, // Habilitar búsqueda
                    "ordering": true, // Habilitar ordenación de columnas
                    "info": true, // Mostrar información de la tabla
                    "lengthChange": true // Permitir cambiar el número de filas por página
                });

                $('#productosLibresTable').DataTable({
                    "paging": true, // Habilitar paginación
                    "searching": true, // Habilitar búsqueda
                    "ordering": true, // Habilitar ordenación de columnas
                    "info": true, // Mostrar información de la tabla
                    "lengthChange": true // Permitir cambiar el número de filas por página
                });
            });
        </script>
    @endpush

</x-app-layout>
