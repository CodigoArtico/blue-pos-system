<x-app-layout>

    @section('title', 'Mesas')
    @push('css')
        <style>
            .mesa-card {
                border-radius: 15px;
                /* Bordes curvos */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                width: 100%;
                /* El ancho será controlado por las clases de Bootstrap */
                text-align: center;
                margin-bottom: 20px;
                /* Espacio entre filas */
                transition: transform 0.3s;
                color: white;
                /* Cambia el color del texto a blanco */
            }

            .mesa-card:hover {
                transform: scale(1.02);
                /* Efecto de aumento al pasar el mouse */
            }

            .mesa-info {
                padding: 20px;
                /* Espacio interno dentro de cada cuadro */
            }

            .mesa-title {
                font-size: 1.25rem;
                font-weight: bold;
            }

            .mesa-status {
                font-size: 1rem;
                margin-bottom: 10px;
            }

            /* Ajusta el margen para añadir más espacio horizontal entre columnas */
            .row .mesa-card {
                margin-right: 0;
                /* No necesitamos margen adicional si ya ocupan 50% */
            }
        </style>
    @endpush

    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Mesas <i class="fa-solid fa-table fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Mesas</li>
        </ol>

        <div class="card">
            <div class="card-header mb-2 text-end">
                <!-- Botón para abrir el modal de crear -->
                <a href="#" class="btn btn-dark round icon" data-bs-toggle="modal" data-bs-target="#addTableModal">
                    <i class="fas fa-plus fa-beat"></i>
                </a>
            </div>

            <!-- Contenido de la tarjeta (Mostrar Mesas como Cuadros) -->
            <div class="card-body">
                <div class="row">
                    @foreach ($mesas as $mesa)
                        <div class="col-lg-6 col-md-4 mb-4">
                            <div class="mesa-card {{ $mesa->estado == 0 ? '' : 'bg-dark' }}">
                                <div class="mesa-info p-3">
                                    <h5
                                        class="mesa-title text-uppercase {{ $mesa->estado == 0 ? 'text-dark' : 'text-white' }}">
                                        {{ $mesa->nombre }}</h5>
                                    <p class="mesa-status {{ $mesa->estado == 0 ? 'text-dark' : 'text-white' }} ">
                                        @if ($mesa->estado == 0)
                                            <i class="fas fa-check-circle text-success"></i> Libre
                                            @if ($mesa->reserva == 1)
                                                <i
                                                    class="fa-solid fa-person-walking-dashed-line-arrow-right text-warning"></i>
                                                Reservada
                                            @endif
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i> Ocupada
                                            @if ($mesa->reserva == 1)
                                                <i
                                                    class="fa-solid fa-person-walking-dashed-line-arrow-right text-warning"></i>
                                                Reservada
                                            @endif
                                        @endif
                                    </p>
                                    @can('detalle-mesas')
                                        <a href="{{ route('mesas.show', $mesa->id) }}"
                                            class="btn icon btn-primary"><i class="fa-solid fa-bars me-2"></i>Detalles</a>
                                    @endcan
                                    @if ($mesa->estado == 1)
                                        <!-- Botón para abrir la ventana emergente con ícono -->
                                        <button class="btn btn-info icon"
                                            onclick="abrirVentanaTicket({{ $mesa->id }})">
                                            <i class="fas fa-ticket-alt"></i> Ticket
                                        </button>
                                        {{-- script para abrir ventana emergente --}}
                                        <script>
                                            function abrirVentanaTicket(id) {
                                                // Definir las dimensiones de la ventana
                                                const width = 350;
                                                const height = 500;

                                                // Calcular las posiciones x e y para centrar la ventana
                                                const left = (screen.width / 2) - (width / 2);
                                                const top = (screen.height / 2) - (height / 2);

                                                // Abrir la nueva ventana con las dimensiones y posiciones calculadas
                                                window.open(`/ticket-mesa/${id}`, 'Ticket de la Mesa',
                                                    `width=${width},height=${height},top=${top},left=${left}`);
                                            }
                                        </script>
                                    @endif
                                    <button class="btn icon btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editTableModal" data-id="{{ $mesa->id }}"
                                        data-nombre="{{ $mesa->nombre }}" data-estado="{{ $mesa->estado }}"
                                        data-reserva="{{ $mesa->reserva }}"
                                        data-nombre_reserva="{{ $mesa->nombre_reserva }}"
                                        data-hora_reserva="{{ $mesa->hora_reserva }}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </button>
                                    <!-- Botón para eliminar la mesa -->
                                    <button class="btn btn-danger icon delete-mesa" data-id="{{ $mesa->id }}">
                                        <i class="fas fa-trash me-2"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para añadir una nueva mesa -->
    <div class="modal fade" id="addTableModal" tabindex="-1" aria-labelledby="addTableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="addTableModalLabel">Añadir Mesa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTableForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label text-dark">Nombre de la Mesa</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label text-dark">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="0">Libre</option>
                                <option value="1">Ocupada</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary"><i class="fa-regular fa-square-plus me-2"></i>Añadir Mesa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para editar mesa -->
    <div class="modal fade" id="editTableModal" tabindex="-1" aria-labelledby="editTableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="editTableModalLabel">Editar Mesa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-dark">
                    <form id="editTableForm">
                        @csrf
                        @method('PUT') <!-- Método PUT para la actualización -->
                        <input type="hidden" id="mesa_id" name="mesa_id">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Mesa</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <!-- Estado (1 = Ocupada, 0 = Libre) -->
                            <select class="form-select" id="estado" name="estado">
                                <option value="0">Libre</option>
                                <option value="1">Ocupada</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="reserva" name="reserva">
                            <label class="form-check-label" for="reserva">¿Reservada?</label>
                        </div>

                        <!-- Campos de reserva -->
                        <div class="mb-3 d-none" id="reservaFields">
                            <label for="nombre_reserva" class="form-label">Nombre del Reservador</label>
                            <input type="text" class="form-control" id="nombre_reserva" name="nombre_reserva">

                            <label for="hora_reserva" class="form-label">Hora de Reserva</label>
                            <input type="datetime-local" class="form-control" id="hora_reserva" name="hora_reserva">
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk me-2"></i>Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            // Rellenar el formulario de edición con los datos de la mesa
            $('#editTableModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var mesaId = button.data('id');
                var nombre = button.data('nombre');
                var estado = button.data('estado');
                var reserva = button.data('reserva');
                var nombreReserva = button.data('nombre_reserva');
                var horaReserva = button.data('hora_reserva');

                // console.log('Mesa ID:', mesaId);
                // console.log('Nombre:', nombre);
                // console.log('Estado:', estado);
                // console.log('Reserva:', reserva);
                // console.log('Nombre Reserva:', nombreReserva);
                // console.log('Hora Reserva:', horaReserva);

                var modal = $(this);
                modal.find('#mesa_id').val(mesaId);
                modal.find('#nombre').val(nombre);
                modal.find('#estado').val(estado.toString()); // Asegúrate de convertir a string
                modal.find('#reserva').prop('checked', reserva == 1); // Verifica si es `1` (reservada)

                // Mostrar u ocultar los campos de reserva según el valor de `reserva`
                if (reserva == 1) {
                    modal.find('#reservaFields').removeClass('d-none');
                    modal.find('#nombre_reserva').val(nombreReserva);
                    modal.find('#hora_reserva').val(horaReserva);
                } else {
                    modal.find('#reservaFields').addClass('d-none');
                }
            });

            // Mostrar/ocultar campos de reserva si el checkbox se marca/desmarca
            $('#reserva').change(function() {
                if ($(this).prop('checked')) {
                    $('#reservaFields').removeClass('d-none');
                } else {
                    $('#reservaFields').addClass('d-none');
                }
            });

            // Enviar formulario de edición
            $('#editTableForm').on('submit', function(event) {
                event.preventDefault(); // Previene el envío normal del formulario
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const formData = new FormData(this);

                // Enviar formulario con AJAX
                fetch('{{ route('mesas.update', '') }}/' + document.getElementById('mesa_id').value, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Indica que es una petición AJAX
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            reproducirSonidoSuccess();
                            mostrarToast('success', data.message);
                            $('#editTableModal').modal('hide');
                            setTimeout1medio5sg(() => {
                                // console.log("Se ejecutó el callback y la página se recargará.");
                            }, 1500);
                        } else {
                            alert('Error al actualizar la mesa');
                        }
                    })
                    .catch(error => console.error('Error:', error)).finally(() => {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    });
            });

            // Enviar formulario de añadir mesa
            document.getElementById('addTableForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Previene el envío normal del formulario
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const formData = new FormData(this);

                // Envío del formulario como JSON
                fetch('{{ route('mesas.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Indica que es una petición AJAX
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Aquí puedes agregar lógica para mostrar un mensaje de éxito o actualizar la lista de mesas
                            reproducirSonidoSuccess();
                            mostrarToast('success', data.message);
                            // Por ejemplo, puedes cerrar el modal y reiniciar el formulario
                            document.getElementById('addTableForm').reset();
                            $('#addTableModal').modal('hide');
                            setTimeout1medio5sg(() => {
                                // console.log("Se ejecutó el callback y la página se recargará.");
                            }, 1500);
                        } else {
                            // Manejo de errores
                            alert('Error al añadir la mesa');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    }).finally(() => {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    });
            });

            // Manejar el clic en el botón de eliminar
            $(document).on('click', '.delete-mesa', function() {
                const mesaId = $(this).data('id');

                // Mostrar la confirmación con SweetAlert 2
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar un spinner o indicador de carga
                        showSpinner();

                        // Enviar la solicitud DELETE al servidor
                        fetch(`/mesas/${mesaId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Token CSRF para seguridad
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                },
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    reproducirSonidoSuccess();
                                    // Mostrar un mensaje de éxito con SweetAlert 2
                                    Swal.fire({
                                        title: '¡Eliminado!',
                                        text: data.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 2000, // Cerrar automáticamente después de 2 segundos
                                        timerProgressBar: true, // Mostrar una barra de progreso
                                    }).then(() => {
                                        // Recargar la página o eliminar la mesa del DOM
                                        location
                                    .reload(); // Recargar la página para reflejar los cambios
                                    });
                                } else {
                                    // Mostrar un mensaje de error con SweetAlert 2
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Hubo un error al eliminar la mesa.',
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar',
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Hubo un error al eliminar la mesa.',
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar',
                                });
                            })
                            .finally(() => {
                                // Ocultar el spinner
                                hideSpinner();
                            });
                    }
                });
            });
        </script>
    @endpush

</x-app-layout>
