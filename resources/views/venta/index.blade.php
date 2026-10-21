<x-app-layout>

    @section('title', 'ventas')

    @push('css')
        <!-- Estilos de DataTables -->
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

        <!-- Scripts de jQuery y DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <style>
            .row-not-space {
                width: 110px;
            }

            #ventasTableResgiters {
                display: none;
                /* Oculta la tabla inicialmente */
            }
        </style>
    @endpush

    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Ventas <i class="fa-solid fa-cart-plus fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Menú</a></li>
            <li class="breadcrumb-item active">Ventas</li>
        </ol>

        <div class="row">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="{{ route('ventas.index') }}" method="GET" class="row g-3">
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
                <div class="card h-80">
                    <div class="card-header bg-primary text-white h-10">
                        <center>Monto Total de Ventas</center>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mt-1">
                            <i class="fa-solid fa-sack-dollar fa-xl text-success me-3 mt-4"></i>
                            <h5 class="card-title mb-0">{{ number_format($totalVentas, 2) }} | </h5>
                            <i class="fa-solid fa-sack-dollar fa-xl text-danger me-3 mt-4"></i>
                            <h5 class="card-title mb-0"> {{ number_format($totalVentasAnulados, 2) }}</h5>
                        </div>
                        <p class="card-text">
                            Desde <span
                                class="badge bg-dark mb-1">{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</span><br>
                            Hasta <span
                                class="badge bg-dark">{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="card mb-2 table-responsive">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-venta')
                    <div class="mb-4">
                        <a href="{{ route('ventas.create') }}" style="color: white;">
                            <button class="btn btn-dark icon round">
                                <i class="fas fa-plus"></i>
                            </button>
                        </a>
                    </div>
                @endcan

                <div class="card-header font-semibold" style="color:#000000">
                    <i class="fas fa-table me-1"></i>
                    Registros
                </div>
                <div class="d-flex ">
                    <i data-feather="download"></i>
                </div>
            </div>
            <div class="card-body">
                <table id="ventasTableResgiters" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha y hora</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ventas as $item)
                            <tr>
                                <td>
                                    <strong>
                                        <p class="fw-semibold mb-1" style="color:#000000">
                                            {{ $item->comprobante->tipo_comprobante }}</p>
                                    </strong>
                                    @if ($item->serie)
                                        <p class="text-muted mb-0">{{ $item->serie }}-{{ $item->numero_comprobante }}
                                        </p>
                                    @else
                                        <p class="text-muted mb-0">{{ $item->numero_comprobante }}</p>
                                    @endif
                                </td>
                                <td>
                                    <p class="fw-semibold mb-1"style="color:#000000"><i class="fa-solid fa-person"></i>
                                        {{ ucfirst($item->cliente->persona->tipo_persona) }}
                                    </p>
                                    <p class="text-muted mb-0"><i class="fa-solid fa-signature"
                                            style="color:#000000"></i>{{ $item->cliente->persona->razon_social }}</p>
                                </td>
                                <td>
                                    <div class="row-not-space">
                                        <p class="fw-semibold mb-1"><span class="m-1"><i
                                                    class="fa-solid fa-calendar-days"></i></span>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                                        </p>
                                        <p class="fw-semibold mb-0"><span class="m-1"><i
                                                    class="fa-solid fa-clock"></i></span>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i:s') }}
                                        </p>
                                    </div>
                                </td>
                                <td style="color:#000000"><i class="fa-solid fa-user-pen"></i>
                                    {{ $item->user->name }}
                                </td>

                                <td style="color:#000000">

                                    <i class="fa-solid fa-dollar-sign"></i>
                                    {{ number_format($item->total, 2, '.', ',') }}
                                </td>
                                <td>
                                    @if (
                                        ($item->comprobante->tipo_comprobante == 'FACTURA' || $item->comprobante->tipo_comprobante == 'BOLETA') &&
                                            !isset($item->emisionComprobante))
                                        <!-- Es FACTURA O BOLETA pero no tienen emision -->
                                        <span class="badge {{ $item->estado == 1 ? 'bg-warning' : 'bg-danger' }}">
                                            {{ $item->estado == 1 ? 'Emitido' : 'Anulado' }}
                                        </span>
                                    @else
                                        <!-- Estado general del item -->
                                        <span class="badge {{ $item->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->estado == 1 ? 'Emitido' : 'Anulado' }}
                                        </span>
                                    @endif

                                    <div class="text">
                                        @if (isset($item->comprobante) &&
                                                ($item->comprobante->tipo_comprobante == 'FACTURA' || $item->comprobante->tipo_comprobante == 'BOLETA'))
                                            @if ($item->estado == 1 && isset($item->emisionComprobante->estado_sunat))
                                                {{ $item->ventasEstado->estado_respuesta_sunat ?? '-' }}
                                            @elseif ($item->estado == 0 && isset($item->emisionComprobante) && isset($item->emisionComprobante->bajaComprobante))
                                                {{-- Verifica que estado_sunat esté presente y muestra el valor --}}
                                                baja:{{ $item->emisionComprobante->bajaComprobante->estado_sunat ?? 'no disponible' }}
                                            @else
                                                {{-- Mensaje opcional en caso de que no haya datos a mostrar --}}
                                                {{ substr($item->ventasEstado->estado_respuesta_sunat ?? 'Sin datos de estado', 0, 8) }}
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        {{-- MOSTRAR VENTA PARA MAS DETALLES Y EL TICKET  --}}
                                        @can('mostrar-venta')
                                            <button type="button" class="btn icon btn" data-bs-toggle="modal"
                                                data-bs-target="#verModal" data-id="{{ $item->id }}"
                                                data-estado="{{ $item->estado }}"
                                                data-tipo-comprobante="{{ $item->comprobante->tipo_comprobante }}"
                                                data-emision-comprobante="{{ isset($item->emisionComprobante) ? 1 : 0 }}"
                                                data-emision-sunat="{{ isset($item->emisionComprobante) ? $item->emisionComprobante->estado_sunat : '' }}"
                                                data-baja-sunat="{{ isset($item->emisionComprobante->bajaComprobante) ? $item->emisionComprobante->bajaComprobante->estado_sunat : '' }}"
                                                data-serie="{{ $item->serie }}"
                                                data-correlativo="{{ $item->correlativo }}"
                                                data-numero-comprobante="{{ $item->numero_comprobante }}"
                                                data-mensaje-cdr="{{ isset($item->emisionComprobante) ? $item->mensaje_cdr : '--' }}"
                                                data-xml="{{ isset($item->emisionComprobante) && $item->estado == 1 ? htmlentities(json_encode($item->contenido_xml)) : '--' }}"
                                                {{-- data-sunat="{{ isset($item->emisionComprobante) ? $item->emisionComprobante->estado_sunat : ($item->ventasEstado->estado_respuesta_sunat ?? 'No enviado') }}" --}}
                                                data-cliente="{{ $item->cliente->persona->razon_social }}"
                                                data-vendedor="{{ $item->user->name }}"
                                                data-fecha="{{ $item->created_at->format('d-m-Y') }}"
                                                data-fecha-asignada="{{ \Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y') }}"
                                                data-hora="{{ $item->created_at->format('H:i:s') }}"
                                                data-hora-asignada="{{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i:s') }}"
                                                data-impuesto="{{ $item->impuesto }}"
                                                data-metodo-pago="{{ $item->metodoPago->nombre }}"
                                                data-monto-recibido="{{ $item->monto_recibido }}"
                                                data-numero-operacion="{{ $item->numero_operacion }}"
                                                data-productos='@json($item->productos)'
                                                data-productos-libres='@json($productos_libres[$item->id])'>
                                                <i class="fa-solid fa-eye text-secondary"></i>
                                            </button>

                                            <div class="px-1"></div>
                                            {{-- Botón para abrir el modal --}}
                                            <button type="button" class="btn btn-outline-dark icon"
                                                data-bs-toggle="modal" data-bs-target="#modalTicket{{ $item->id }}">
                                                <i class="fa-solid fa-print"></i>
                                            </button>

                                            {{-- Incluir la vista parcial del modal --}}
                                            @include('venta.partials.ticket-venta', ['venta' => $item])
                                            <!-- NOT VIEW -->
                                        @endcan
                                        <div class="px-1"></div>
                                        @can('eliminar-venta')
                                            @if ($item->estado == 1 && \Carbon\Carbon::parse($item->created_at)->diffInDays(now()) <= 3)
                                                <button type="button" class="btn icon btn-icon btn-delete-sale"
                                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                                    data-id="{{ $item->id }}"
                                                    data-comprobante="{{ $item->comprobante->tipo_comprobante }}"
                                                    data-serie="{{ $item->serie }}"
                                                    data-numero="{{ $item->numero_comprobante }}">
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
        {{-- Modal de vista previa  --}}
        <div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles de la Venta</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí se llenarán los datos dinámicamente -->
                        <div id="venta-detalle"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación genérico -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 text-dark" id="confirmModalLabel">Mensaje de confirmación</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí se insertará dinámicamente el mensaje de confirmación -->
                        <p class="text-dark" id="modal-message"></p>
                        <div class="mt-2">
                            <input type="text" class="form-control" name="motivo"
                                placeholder="Ingrese el motivo" value="Anulación por solicitud del cliente">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <!-- Botón para confirmar eliminación -->
                        <button type="button" class="btn btn-danger btn-confirm-delete"
                            data-id="">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        @include('venta.partials.index-js')
        <script>
            // Mostrar el spinner de DataTables inmediatamente al cargar la página
            showSpinnerDataTables(); // Mostrar el spinner tan pronto como el script se ejecute
            // Mostrar el spinner de DataTables inmediatamente al cargar la página
            document.addEventListener('DOMContentLoaded', function() {
                // console.log('Spinner de DataTables mostrado');

                // Inicializar DataTables
                var table = $('#ventasTableResgiters').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "lengthChange": true,
                    "order": [],
                    "initComplete": function() {
                        // Mostrar la tabla y ocultar el spinner cuando DataTables esté listo
                        setTimeout(function() {
                            $('#ventasTableResgiters').css('display',
                                'table'); // Mostrar la tabla
                            hideSpinnerDataTables(); // Ocultar el spinner
                            // console.log('Tabla mostrada y spinner ocultado');
                        }, 1000); // No hay retraso adicional
                    },
                    "drawCallback": function() {
                        // No es necesario hacer nada aquí para este caso
                    }
                });
            });


            // ELIMINACION 
            // Configurar CSRF Token para todas las solicitudes AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Cuando se muestre el modal, actualiza su contenido
            $('#confirmModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Botón que disparó el modal
                const ventaId = button.data('id');
                const comprobante = button.data('comprobante');
                const serie = button.data('serie');
                const numero = button.data('numero');

                // Construye el mensaje de confirmación dinámico
                const mensaje = `¿Seguro que quieres eliminar la ${comprobante}-${serie}-${numero}?`;
                $(this).find('#modal-message').html(mensaje);

                // Actualiza el atributo data-id del botón de confirmación
                $(this).find('.btn-confirm-delete').data('id', ventaId);
            });
            // Manejar el clic en el botón de confirmar eliminación dentro del modal
            $(document).on('click', '.btn-confirm-delete', function() {
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const ventaId = $(this).data('id');
                // Busca el input de motivo dentro del modal
                const motivo = $('#confirmModal').find('input[name="motivo"]').val().trim();

                if (!ventaId) {
                    mostrarToast('error', 'ID de venta no encontrado.');
                    hideSpinner(); // Ocultar el spinner si falta el ID
                    return;
                }

                if (!motivo) {
                    mostrarToast('error', 'Debe ingresar un motivo para la baja.');
                    hideSpinner(); // Ocultar el spinner si falta el motivo
                    return;
                }

                // Realizar la solicitud AJAX para eliminar y dar de baja
                $.ajax({
                    url: "{{ url('/ventas') }}/" + ventaId,
                    type: 'DELETE',
                    data: {
                        motivo: motivo
                    },
                    success: function(response) {
                        if (response.estado === 'Exitoso') {
                            // Cerrar el modal antes de proceder
                            $('#confirmModal').modal('hide');
                            reproducirSonidoSuccess();
                            mostrarToast(response.icon, response.descripcion);
                        } else {
                            mostrarToast('error', response.descripcion);
                        }
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        let errorMsg = 'Ocurrió un error al eliminar la venta.';
                        if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                            errorMsg = xhr.responseJSON.mensaje;
                        }
                        mostrarToast('error', errorMsg);
                    },
                    complete: function() {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>
