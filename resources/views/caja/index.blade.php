<x-app-layout>
    @section('title', 'Resumen de Caja')

    @push('css')
        <!-- Estilos de DataTables -->
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

        <!-- Scripts de jQuery y DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="main-content container-fluid py-4">
        <h1 class="mb-4 text-center text-dark"><i class="fas fa-cash-register fa-lg"></i> Gestion de
            @if (auth()->user()->can('ver-caja') && auth()->user()->can('ver-gastos'))
                Caja / Gastos
            @elseif (auth()->user()->can('ver-caja'))
                Caja
            @elseif (auth()->user()->can('ver-gastos'))
                Gastos
            @endif
        </h1>

        <!-- Filtro de Fechas -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('caja.index') }}" method="GET" class="row g-3">
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

        @can('ver-caja')
            <div class="row">

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-dark">Saldo Total Acumulado</h3>
                            <canvas id="saldoAcumuladoChart"></canvas>
                        </div>
                    </div>
                    @include('caja.partials.saldo-acumulado')
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-dark">Comparación de Ingresos vs Gastos</h3>

                            <!-- Contenedor de los checkboxes -->
                            <div class="mb-3">
                                <input type="checkbox" id="toggleIngresos" checked>
                                <label for="toggleIngresos">Mostrar Ingresos</label>
                                <input type="checkbox" id="toggleGastos" checked>
                                <label for="toggleGastos">Mostrar Gastos</label>
                            </div>

                            <!-- Canvas para el gráfico -->
                            <canvas id="ingresosGastosChart"></canvas>
                        </div>
                    </div>
                    @include('caja.partials.ingresos-gastos')
                </div>

            </div>

            <!-- Tabla Resumen de Caja -->
            <div class="card shadow-sm">
                <div class="card-header text-dark">
                    <h4 class="text-dark"><i class="fas fa-cash-register"></i> Resumen de Caja</h4>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped" id="resumencaja">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Ventas (Efectivo)</th>
                                <th>Ventas (Otros)</th>
                                <th>Gastos (Efectivo)</th>
                                <th>Gastos (Otros)</th>
                                <th>Saldo Efectivo</th>
                                <th>Saldo Otros</th>
                                <th>Saldo Total</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resumenCaja as $resumen)
                                <tr class="text-dark">
                                    <td>
                                        <div class="row-not-space text-dark">
                                            <p class="fw-semibold mb-1"><span class="m-1"><i
                                                        class="fa-solid fa-calendar-days"></i></span>{{ \Carbon\Carbon::parse($resumen['fecha'])->format('d-m-Y') }}
                                            </p>
                                        </div>
                                    </td>
                                    <td>S/. {{ number_format($resumen['ventas_efectivo'], 2) }}</td>
                                    <td>S/. {{ number_format($resumen['ventas_otros'], 2) }}</td>
                                    <td>S/. {{ number_format($resumen['gastos_efectivo'], 2) }}</td>
                                    <td>S/. {{ number_format($resumen['gastos_otros'], 2) }}</td>
                                    <td>S/. {{ number_format($resumen['saldo_efectivo'], 2) }}</td>
                                    <td>S/. {{ number_format($resumen['saldo_otros'], 2) }}</td>
                                    <td>S/. {{ number_format($resumen['saldo_total'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-outline-dark icon" data-bs-toggle="modal"
                                            data-bs-target="#detallesModal{{ $loop->index }}">
                                            <i class="fas fa-eye text-info"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endcan

        @can('ver-gastos')
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-dark">Métodos de Pago- Gastos</h3>
                            <canvas id="metodosPagoChart"></canvas>
                        </div>
                    </div>
                    @include('caja.partials.gastos-metodopago')
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="text-dark">Ventas y Gastos por Día</h3>
                            <canvas id="ventasGastosChart"></canvas>
                        </div>
                    </div>
                    @include('caja.partials.gastos-pordia')
                </div>
            </div>

            <!-- Listado de Gastos -->
            <div class="card shadow-sm mt-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-dark">Listado de Gastos</h4>
                    @can('crear-gastos')
                        <button class="btn btn-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#addGastoModal">
                            <i class="fas fa-plus"></i> Añadir Gasto
                        </button>
                    @endcan
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered" id="listagastos">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Método de Pago</th>
                                <th>Monto</th>
                                <th>User</th>
                                <th>Estado</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gastosRango as $gasto)
                                <tr class="text-dark">
                                    <td>
                                        <div class="row-not-space">
                                            <p class="fw-semibold mb-1"><span class="m-1"><i
                                                        class="fa-solid fa-calendar-days"></i></span>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d-m-Y') }}
                                            </p>
                                            <p class="fw-semibold mb-0"><span class="m-1"><i
                                                        class="fa-solid fa-clock"></i></span>{{ \Carbon\Carbon::parse($gasto->fecha)->format('H:i:s') }}
                                            </p>
                                        </div>
                                    </td>
                                    <td>{{ $gasto->descripcion }}</td>
                                    <td>{{ $gasto->metodoPago->nombre ?? 'N/A' }}</td>
                                    <td>S/. {{ number_format($gasto->monto, 2) }}</td>
                                    <td>
                                        <i class="fa-regular fa-circle-user"></i> {{ $gasto->usuario->name ?? 'N/A' }}
                                        <!-- Muestra el usuario -->
                                    </td>
                                    <td>
                                        <center>
                                            @if ($gasto->estado == 1)
                                                <i class="fa-solid fa-circle-check text-success"></i>
                                            @else
                                                <i class="fa-solid fa-circle-xmark text-danger"></i>
                                            @endif
                                        </center>
                                    </td>
                                    <td>
                                        @if ($gasto->estado == 1)
                                            @can('editar-gastos')
                                                <button class="btn btn-outline-dark icon" data-bs-toggle="modal"
                                                    data-bs-target="#editGastoModal{{ $gasto->id }}">
                                                    <i class="fas fa-edit text-info"></i>
                                                </button>
                                            @endcan
                                            @can('eliminar-gastos')
                                                <button class="btn btn-outline-dark icon delete-gasto"
                                                    data-id="{{ $gasto->id }}">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            @endcan
                                        @else
                                            @foreach (['editar-gastos', 'eliminar-gastos'] as $permission)
                                                @can($permission)
                                                    <button disabled class="btn btn-outline-dark icon text-dark">
                                                        --
                                                    </button>
                                                @endcan
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endcan

        <!-- Modales -->
        @foreach ($resumenCaja as $resumen)
            <div class="modal fade" id="detallesModal{{ $loop->index }}" tabindex="-1"
                aria-labelledby="detallesModalLabel{{ $loop->index }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detalles de Gastos -
                                {{ \Carbon\Carbon::parse($resumen['fecha'])->format('d-m-Y') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resumen['gastos_detalle'] as $gasto)
                                        <tr>
                                            <td>{{ $gasto->descripcion }}</td>
                                            <td>S/. {{ number_format($gasto->monto, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d-m-Y H:i:s') }}</td>
                                            <td>
                                                @if ($gasto->estado == 1)
                                                    <i class="fa-solid fa-circle-check text-success"></i>
                                                @else
                                                    <i class="fa-solid fa-circle-xmark text-danger"></i>
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
        @endforeach

        @can('crear-gastos')
            {{-- modal añadir gasto --}}
            <div class="modal fade" id="addGastoModal" tabindex="-1" aria-labelledby="addGastoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog"> <!-- modal-lg para más espacio -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addGastoModalLabel">Agregar Gasto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addGastoForm">
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción del Gasto</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea> <!-- Aumenté las filas del textarea -->
                                </div>
                                <div class="mb-3">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                        <option value="" disabled selected>Seleccione un método</option>
                                        <!-- Placeholder para el select -->
                                        @foreach ($metodosPago as $metodo)
                                            <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="monto" class="form-label">Monto</label>
                                    <input type="number" step="0.01" class="form-control" id="monto"
                                        name="monto" required>
                                </div>
                                <div class="d-flex d-flex justify-content-center">
                                    <button type="submit" class="btn btn-dark icon"><i
                                            class="fa-regular fa-floppy-disk"></i> Guardar Gasto</button>
                                    <!-- Botón estilizado -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        @can('editar-gastos')
            <!-- Modales para Editar Gastos -->
            @foreach ($gastosRango as $gasto)
                <div class="modal fade" id="editGastoModal{{ $gasto->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog"> <!-- modal-lg para más espacio -->
                        <div class="modal-content">
                            <div class="modal-header bg-light text-dark"> <!-- Fondo azul y texto blanco -->
                                <h5 class="modal-title text-dark">Editar Gasto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form class="edit-gasto-form" data-id="{{ $gasto->id }}">
                                    <div class="mb-3">
                                        <label for="descripcion-{{ $gasto->id }}" class="form-label">Descripción del
                                            Gasto</label>
                                        <textarea class="form-control" id="descripcion-{{ $gasto->id }}" name="descripcion" rows="4" required>{{ $gasto->descripcion }}</textarea> <!-- Ampliamos el área de texto -->
                                    </div>
                                    <div class="mb-3">
                                        <label for="metodo_pago-{{ $gasto->id }}" class="form-label">Método de
                                            Pago</label>
                                        <select class="form-select" id="metodo_pago-{{ $gasto->id }}"
                                            name="metodo_pago" required>
                                            @foreach ($metodosPago as $metodo)
                                                <option value="{{ $metodo->id }}"
                                                    {{ $metodo->id == $gasto->metodo_pago_id ? 'selected' : '' }}>
                                                    {{ $metodo->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="monto-{{ $gasto->id }}" class="form-label">Monto</label>
                                        <input type="number" step="0.01" class="form-control"
                                            id="monto-{{ $gasto->id }}" name="monto" value="{{ $gasto->monto }}"
                                            required>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn btn-dark icon"><i
                                                class="fa-regular fa-floppy-disk"></i> Guardar Cambio</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endcan

    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                $('#resumencaja, #listagastos').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    lengthChange: true
                });
            });

            document.getElementById('addGastoForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const formData = {
                    descripcion: document.getElementById('descripcion').value,
                    metodo_pagos_id: document.getElementById('metodo_pago').value,
                    monto: document.getElementById('monto').value,
                };

                fetch("{{ route('gastos.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(formData),
                    })
                    .then(response => response.ok ? response.json() : Promise.reject('Error al guardar'))
                    .then(data => {
                        // Cierra el modal de añadir gasto
                        $('#addGastoModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout1medio5sg(() => {}, 1500); // Se actualizará en 1500ms (1.5 segundos)
                    })
                    .catch(error => {
                        reproducirSonidoError();
                        mostrarToast('error', error);
                    }).finally(() => {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    });


            });
            // Función para eliminar un gasto con SweetAlert
            $('.delete-gasto').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Este gasto se eliminará de manera permanente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar el spinner al iniciar la solicitud
                        showSpinner();
                        fetch(`{{ url('gastos') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.ok ? response.json() : Promise.reject(
                                'Error al eliminar'))
                            .then(data => {
                                reproducirSonidoSuccess();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500, // Cerrar automáticamente después de 2 segundos
                                    timerProgressBar: true, // Mostrar una barra de progreso
                                });
                                setTimeout1medio5sg(() => {},
                                    1500); // Se actualizará en 1500ms (1.5 segundos)
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error al eliminar',
                                    text: 'Hubo un problema al eliminar el gasto: ' + error,
                                    showConfirmButton: true,
                                });
                            }).finally(() => {
                                // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                                hideSpinner();
                            });
                    }
                });
            });

            // Función para editar un gasto
            $('.edit-gasto-form').submit(function(e) {
                e.preventDefault();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const id = $(this).data('id');
                const formData = {
                    descripcion: $(`#descripcion-${id}`).val(),
                    metodo_pago: $(`#metodo_pago-${id}`).val(),
                    monto: $(`#monto-${id}`).val(),
                };

                fetch(`{{ url('gastos') }}/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(formData),
                    })
                    .then(response => response.ok ? response.json() : Promise.reject('Error al actualizar'))
                    .then(data => {
                        // Cierra el modal de edición específico
                        $(`#editGastoModal${id}`).modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout1medio5sg(() => {}, 1500); // Se actualizará en 1500ms (1.5 segundos)
                    })
                    .catch(error => {
                        // Manejo del error
                        reproducirSonidoError();
                        mostrarToast('error', error);
                    }).finally(() => {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    });

            });
        </script>
    @endpush
</x-app-layout>
