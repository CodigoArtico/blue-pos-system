<x-app-layout>
    @section('title', 'Perfil')

    @push('css')
        <!-- Opcional: Añade CSS personalizado aquí -->
    @endpush

    <div class="container px-4">
        <h1 class="mt-2 text-center text-dark">Mi Perfil <i class="fa-regular fa-id-badge fa-lg"></i></h1>

        <div class="card shadow-lg mt-3 mx-auto" style="max-width: 1400px;">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h4 class="mb-0 text-dark">Información del Perfil</h4>
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    @can('super-boton-obtener-sistema')
                        <H6 class="text-dark">
                            @include('components.obtener-sistema.obtener')
                        </H6>
                    @endcan
                    <div class="col-md-4 text-center">
                        <img src="{{ $user->img
                            ? (filter_var($user->img, FILTER_VALIDATE_URL)
                                ? $user->img
                                : asset('storage/img_user/' . $user->img))
                            : asset('images/profile1.png') }}"
                            alt="Imagen de Perfil" class="img-fluid rounded-circle mb-3"
                            style="width: 150px; height: 150px;">
                    </div>
                    <div class="col-md-8">
                        <h4 class="card-title text-dark"><i class="fa-solid fa-user"></i> {{ $user->name }}</h4>
                        <p class="card-text text-muted"><i class="fa-solid fa-envelope-circle-check"></i>
                            {{ $user->email }}</p>
                        <p class="card-text">
                            <strong class="text-dark">Estado:</strong>
                            <span class="badge {{ $user->estado == '1' ? 'bg-success' : 'bg-secondary' }}">
                                @if ($user->estado == '1')
                                    <i class="fas fa-check"></i> <!-- Check Icon -->
                                @else
                                    <i class="fas fa-times"></i> <!-- X Icon -->
                                @endif
                            </span>
                        </p>
                        <div class="d-flex justify-content-start mt-4">
                            @can('editar-perfil')
                                <button class="btn btn-outline-primary me-4" data-bs-toggle="modal"
                                    data-bs-target="#editarPerfilModal">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            @endcan
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button class="btn btn-outline-danger">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted text-center text-dark">
                Última actualización: {{ $user->updated_at->format('d-m-Y') }}
            </div>
        </div>
    </div>
    @can('editar-perfil')
        <!-- Modal para editar Perfil -->
        <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarPerfilLabel">Editar Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editarPerfilForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <!-- Método PUT para actualización -->

                            <div class="mb-3">
                                <label for="nombreUsuario" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombreUsuario" name="name"
                                    value="{{ $user->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="emailUsuario" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="emailUsuario" name="email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="passwordUsuario" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="passwordUsuario" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="imagenUsuario" class="form-label">Imagen de Perfil</label>
                                <input type="file" class="form-control" id="imagenUsuario" name="img"
                                    accept="image/*">
                                @if ($user->img)
                                    <small class="form-text text-muted">Imagen actual:</small>
                                    <img src="{{ $user->img
                                        ? (filter_var($user->img, FILTER_VALIDATE_URL)
                                            ? $user->img
                                            : asset('storage/img_user/' . $user->img))
                                        : asset('images/profile1.png') }}"
                                        alt="Imagen Actual" class="img-fluid rounded-circle mt-2"
                                        style="width: 100px; height: 100px;">
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" form="editarPerfilForm" class="btn btn-primary"><i
                                class="fa-regular fa-floppy-disk me-2"></i>Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @push('js')
        <script>
            $(document).ready(function() {
                $('#editarPerfilForm').on('submit', function(e) {
                    e.preventDefault(); // Evita la acción predeterminada del formulario

                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();

                    // Crear un objeto FormData para manejar archivos
                    var formData = new FormData(this);
                    formData.append('_method', 'PUT'); // Agregar método PUT

                    $.ajax({
                        url: "{{ route('perfil.update') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            $('#editarPerfilModal .modal-footer button').prop('disabled', true);
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Cerrar el modal
                                $('#editarPerfilModal').modal('hide');
                                reproducirSonidoSuccess();

                                // Mostrar mensaje de éxito con barra de progreso
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                    timerProgressBar: true, // Mostrar la barra de progreso
                                    willClose: () => {
                                        location
                                            .reload(); // Recargar la página cuando se cierre el modal
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            $('#editarPerfilModal .modal-footer button').prop('disabled', false);

                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                var errorMessages = '';

                                $.each(errors, function(key, messages) {
                                    errorMessages += messages.join('<br>') + '<br>';
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Errores de Validación',
                                    html: errorMessages,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error al actualizar el perfil.',
                                });
                            }
                        },
                        complete: function() {
                            $('#editarPerfilModal .modal-footer button').prop('disabled', false);
                            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                            hideSpinner();
                        }
                    });
                });
            });
        </script>
    @endpush

</x-app-layout>
