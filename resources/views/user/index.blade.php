<x-app-layout>

    @section('title', 'usuarios')

    @push('css')
        <!-- Estilos de DataTables -->
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

        <!-- Scripts de jQuery y DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    @endpush

    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Usuarios <i class="fa-solid fa-users fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">

                <div class="card-header font-semibold text-dark">
                    <i class="fas fa-table me-1"></i>
                    Registros
                </div>
                @can('crear-user')
                    <button type="button" class="btn btn-dark icon round" onclick="$('#crearUsuarioModal').modal('show')">
                        <i class="fas fa-plus"></i>
                    </button>
                @endcan
            </div>
            <div class="card-body px-1 pb-1 table-responsive">
                <table id="table1" class="table table-hover table-bordered fs-6">
                    <thead>
                        <tr>
                            <th>NOMBRES</th>
                            <th>Email</th>
                            <th>Rol</th>
                            @can('ver-sesiones')
                                <th>Session</th> <!-- Nuevo campo -->
                            @endcan
                            <th><i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $item)
                            <tr>
                                <td class="font-semibold text-dark"><i class="fa-regular fa-circle-user"></i>
                                    {{ $item->name }}</td>
                                <td class="text-dark"><i class="fa-solid fa-envelope-circle-check"></i>
                                    {{ $item->email }}</td>
                                <td><i class="fa-regular fa-address-book" style="color:#000000"></i>
                                    {{ $item->getRoleNames()->first() }}</td>
                                {{-- ver sesiones de los usuarios  --}}
                                @can('ver-sesiones')
                                    <td>
                                        <button title="Ver Sesiones" data-bs-toggle="modal"
                                            data-bs-target="#verModal-{{ $item->id }}"
                                            class="btn btn-datatable btn icon btn-transparent-dark me-2">
                                            <i class="fa-solid fa-map-location-dot" style="color:#000000"></i>
                                        </button>
                                    </td>
                                @endcan <!-- Mostrar número de sesiones -->
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                        @can('editar-user')
                                            <div>
                                                <button type="button" class="btn btn icon"
                                                    onclick="openEditUserModal({{ $item->id }})">
                                                    <i class="fa-regular fa-pen-to-square" style="color: #52d4ff;"></i>
                                            </div>
                                        @endcan
                                        <div class="px-1"></div>
                                        <div>
                                            <!------Eliminar user---->
                                            @can('eliminar-user')
                                                @if ($item->getRoleNames()->first() !== 'super_admin')
                                                    <button type="button" class="btn btn icon"
                                                        onclick="openDeleteModal({{ $item->id }}, '{{ $item->name }}')">
                                                        <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @can('ver-sesiones')
                                <!-- Modal para ver detalles de sesiones -->
                                <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Detalles de Sesiones de
                                                    {{ $item->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (isset($sessions[$item->id]) && count($sessions[$item->id]) > 0)
                                                    <div class="mb-3">
                                                        <ul class="list-group">
                                                            @foreach ($sessions[$item->id] as $session)
                                                                <li class="list-group-item">
                                                                    <!-- Contenedor con scroll horizontal -->
                                                                    <div class="overflow-auto" style="white-space: nowrap;">
                                                                        <strong>ID de Sesión:</strong> {{ $session->id }}
                                                                        <br>
                                                                        <strong>IP Address:</strong>
                                                                        {{ $session->ip_address }}
                                                                        <br>
                                                                        <strong>User Agent:</strong>
                                                                        {{ $session->user_agent }} <br>
                                                                        <strong>Última Actividad:</strong>
                                                                        {{ date('Y-m-d H:i:s', $session->last_activity) }}
                                                                    </div>
                                                                    <hr>
                                                                    <!-- Botón de Cerrar Sesión debajo del contenido -->
                                                                    <button class="btn btn-danger btn-sm icon d-block mt-2"
                                                                        onclick="cerrarSesion('{{ $session->id }}')">
                                                                        <i class="fa-solid fa-right-from-bracket"></i>
                                                                        Cerrar Sesión
                                                                    </button>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @else
                                                    <p class="text-center">No hay sesiones para este usuario.</p>
                                                @endif

                                                <!-- Botón para cerrar todas las sesiones -->
                                                <button class="btn btn-dark icon mt-3"
                                                    onclick="cerrarTodasSesiones('{{ $item->id }}')">
                                                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Todas las Sesiones
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header font-semibold text-dark">
                <i class="fas fa-table me-1"></i>
                Registros Usuarios Eliminados
            </div>
            <div class="card-body px-1 pb-1 table-responsive">
                <table id="TableUsersPasados" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>CD</th>
                            <th>Nombres</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usersDelete as $item)
                            <tr>
                                <td>{{ Str::random(4) }}</td>
                                <td class="font-semibold text-dark"><i class="fa-regular fa-circle-user"></i>
                                    {{ $item->name }}</td>
                                <td class="text-dark"><i class="fa-solid fa-envelope-circle-check"></i>
                                    {{ $item->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('crear-user')
        <!-- Modal para crear un usuario -->
        <div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearUsuarioLabel">Registrar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-crear-usuario">
                            @csrf
                            <!-- Nombre -->
                            <div class="row mb-4">
                                <label for="name" class="col-lg-2 col-form-label">Nombres:</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name" id="name" class="form-control" required>
                                    <small class="invalid-feedback" id="error_name"></small>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="row mb-4">
                                <label for="email" class="col-lg-2 col-form-label">Email:</label>
                                <div class="col-lg-10">
                                    <input type="email" name="email" id="email" class="form-control" required>
                                    <small class="invalid-feedback" id="error_email"></small>
                                </div>
                            </div>
                            <!-- Contraseña -->
                            <div class="row mb-4">
                                <label for="password" class="col-lg-2 col-form-label">Contraseña:</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="mínimo 8 caracteres" required>
                                    <small class="invalid-feedback" id="error_password"></small>
                                </div>
                            </div>
                            <!-- Confirmar Contraseña -->
                            <div class="row mb-4">
                                <label for="password_confirm" class="col-lg-2 col-form-label">Confirmar:</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password_confirm" id="password_confirm"
                                        class="form-control" placeholder="mínimo 8 caracteres" required>
                                    <small class="invalid-feedback" id="error_password_confirm"></small>
                                </div>
                            </div>
                            <!-- Rol -->
                            <div class="row mb-4">
                                <label for="role" class="col-lg-2 col-form-label">Rol:</label>
                                <div class="col-lg-10">
                                    <select name="role" id="role" class="form-select" required>
                                        <option value="" disabled selected>Seleccione:</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="invalid-feedback" id="error_role"></small>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i
                                        class="fa-regular fa-floppy-disk me-2"></i>Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('editar-user')
        <!-- Modal para editar usuario -->
        <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarUsuarioLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-usuario">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="user_id" id="user_id">

                            <!-- Nombre -->
                            <div class="row mb-4">
                                <label for="edit_name" class="col-lg-2 col-form-label">Nombre:</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name" id="edit_name" class="form-control" required>
                                    <small class="invalid-feedback" id="edit_error_name"></small>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="row mb-4">
                                <label for="edit_email" class="col-lg-2 col-form-label">Email:</label>
                                <div class="col-lg-10">
                                    <input type="email" name="email" id="edit_email" class="form-control" required>
                                    <small class="invalid-feedback" id="edit_error_email"></small>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="row mb-4">
                                <label for="edit_password" class="col-lg-2 col-form-label">Contraseña:</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password" id="edit_password" class="form-control">
                                    <small class="invalid-feedback" id="edit_error_password"></small>
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="row mb-4">
                                <label for="edit_password_confirm" class="col-lg-2 col-form-label">Confirmar:</label>
                                <div class="col-lg-10">
                                    <input type="password" name="password_confirm" id="edit_password_confirm"
                                        class="form-control">
                                    <small class="invalid-feedback" id="edit_error_password_confirm"></small>
                                </div>
                            </div>

                            <!-- Rol -->
                            <div class="row mb-4">
                                <label for="edit_role" class="col-lg-2 col-form-label">Rol:</label>
                                <div class="col-lg-10">
                                    <select name="role" id="edit_role" class="form-select" required>
                                        <option value="" disabled>Seleccione:</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="invalid-feedback" id="edit_error_role"></small>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i
                                        class="fa-regular fa-floppy-disk me-2"></i>Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('eliminar-user')
        <!-- Modal de confirmación -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Mensaje de Confirmación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar al usuario <strong class="text-dark" id="deleteUserName"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @push('js')
        <script>
            $(document).ready(function() {
                // inicar data tables de productos table
                $('#TableUsersPasados').DataTable({
                    "paging": true, // Habilitar paginación
                    "searching": true, // Habilitar búsqueda
                    "ordering": true, // Habilitar ordenación de columnas
                    "info": true, // Mostrar información de la tabla
                    "lengthChange": true, // Permitir cambiar el número de filas por página
                    "order": [] // Evita que DataTables ordene automáticamente
                });
            });
            // Función para limpiar errores del formulario de creación
            function clearCreateFormErrors() {
                ['name', 'email', 'password', 'password_confirm', 'role'].forEach(field => {
                    const input = document.querySelector(`#${field}`);
                    const errorDiv = document.querySelector(`#error_${field}`);
                    if (input && errorDiv) {
                        input.classList.remove('is-invalid');
                        errorDiv.textContent = '';
                    }
                });
            }

            // Función para limpiar errores del formulario de edición
            function clearEditFormErrors() {
                ['name', 'email', 'password', 'password_confirm', 'role'].forEach(field => {
                    const input = document.querySelector(`#edit_${field}`);
                    const errorDiv = document.querySelector(`#edit_error_${field}`);
                    if (input && errorDiv) {
                        input.classList.remove('is-invalid');
                        errorDiv.textContent = '';
                    }
                });
            }

            // Limpiar errores al cerrar los modales
            $('#crearUsuarioModal').on('hidden.bs.modal', clearCreateFormErrors);
            $('#editarUsuarioModal').on('hidden.bs.modal', clearEditFormErrors);

            // Limpiar errores al abrir los modales
            $('#crearUsuarioModal').on('show.bs.modal', clearCreateFormErrors);
            $('#editarUsuarioModal').on('show.bs.modal', clearEditFormErrors);

            // Crear un usuario 
            document.querySelector('#form-crear-usuario').addEventListener('submit', async function(e) {
                e.preventDefault();
                // Mostrar el spinner antes de hacer la solicitud
                showSpinner(); // Llamada a la función para mostrar el spinner
                // Limpiar errores anteriores
                clearCreateFormErrors();

                const formData = new FormData(this);
                const jsonData = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    password_confirm: formData.get('password_confirm'),
                    role: formData.get('role')
                };
                const storeUrl = '{{ route('users.store') }}';

                try {
                    const response = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' // Añadir este encabezado
                        },
                        body: JSON.stringify(jsonData)
                    });

                    const data = await response.json();
                    // console.log('Respuesta del servidor (Crear):', data); // Log de respuesta

                    if (response.ok && data.success) {
                        $('#crearUsuarioModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else if (response.status === 422) {
                        reproducirSonidoAlert();
                        // console.log('Errores de validación (Crear):', data.errors); // Log de errores

                        // Mostrar errores de validación
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = document.querySelector(`#${field}`);
                            const errorDiv = document.querySelector(`#error_${field}`);
                            if (input && errorDiv) {
                                input.classList.add('is-invalid');
                                errorDiv.textContent = messages.join(', ');
                            }
                        }
                    } else {
                        reproducirSonidoError();
                        mostrarToast('error', data.message || 'Ocurrió un error al registrar el usuario.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error de red (Crear):', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner una vez la petición haya terminado
                    hideSpinner();
                }
            });


            // Editar usuario
            async function openEditUserModal(userId) {
                // Limpiar errores anteriores
                clearEditFormErrors();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                try {
                    const response = await fetch(`/users/${userId}/edit`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' // Añadir este encabezado
                        }
                    });
                    const data = await response.json();
                    // console.log('Datos del usuario (Editar):', data); // Log de datos

                    if (data.success) {
                        document.querySelector('#user_id').value = userId;
                        document.querySelector('#edit_name').value = data.user.name;
                        document.querySelector('#edit_email').value = data.user.email;
                        document.querySelector('#edit_role').value = data.user.role;

                        $('#editarUsuarioModal').modal('show');
                    } else {
                        mostrarToast('error', 'Error al cargar los datos del usuario.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error de red (Editar):', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            }
            document.querySelector('#form-editar-usuario').addEventListener('submit', async function(e) {
                e.preventDefault();

                // Limpiar errores anteriores
                clearEditFormErrors();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const formData = new FormData(this);
                const userId = formData.get('user_id');
                const jsonData = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    password_confirm: formData.get('password_confirm'),
                    role: formData.get('role')
                };

                try {
                    const response = await fetch(`/users/${userId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json' // Añadir este encabezado
                        },
                        body: JSON.stringify(jsonData)
                    });

                    const data = await response.json();
                    // console.log('Respuesta del servidor (Editar):', data); // Log de respuesta

                    if (response.ok && data.success) {
                        $('#editarUsuarioModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else if (response.status === 422) {
                        reproducirSonidoAlert();
                        // console.log('Errores de validación (Editar):', data.errors); // Log de errores

                        // Mostrar errores de validación
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = document.querySelector(`#edit_${field}`);
                            const errorDiv = document.querySelector(`#edit_error_${field}`);
                            if (input && errorDiv) {
                                input.classList.add('is-invalid');
                                errorDiv.textContent = messages.join(', ');
                            }
                        }
                    } else {
                        mostrarToast('error', data.message || 'Ocurrió un error al actualizar el usuario.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error de red (Editar):', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            });


            //eliminar un usuario
            // Función que abre el modal y muestra el nombre del usuario
            function openDeleteModal(userId, userName) {
                // Mostrar el nombre del usuario en el modal
                document.getElementById('deleteUserName').textContent = userName;

                // Al hacer clic en "Confirmar", llamamos a la función para eliminar al usuario
                document.getElementById('confirmDeleteBtn').onclick = function() {
                    deleteUser(userId); // Llamamos a la función deleteUser con el userId
                };

                // Abrimos el modal
                $('#confirmDeleteModal').modal('show');
            }
            async function deleteUser(userId) {
                const deleteUrl = `/users/${userId}`;
                // Mostrar el spinner antes de hacer la solicitud
                showSpinner(); // Llamada a la función para mostrar el spinner
                try {
                    const response = await fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        $('#confirmDeleteModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        reproducirSonidoAlert();
                        mostrarToast('error', data.message || 'Ocurrió un error al eliminar el usuario.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner una vez la petición haya terminado
                    hideSpinner();
                }
            }


            //sesiones de usurios y otros
            function cerrarSesion(sessionId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Deseas cerrar esta sesión?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar',
                    cancelButtonText: 'Cancelar',
                    dangerMode: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/sessions/${sessionId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire('Sesión cerrada exitosamente.', {
                                        icon: 'success',
                                    }).then(() => {
                                        location.reload(); // Recargar la página para actualizar la lista
                                    });
                                } else {
                                    Swal.fire('Error al cerrar la sesión.', {
                                        icon: 'error',
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error al cerrar la sesión.', {
                                    icon: 'error',
                                });
                            });
                    }
                });
            }

            function cerrarTodasSesiones(userId) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Deseas cerrar todas las sesiones para este usuario?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar todas',
                    cancelButtonText: 'Cancelar',
                    dangerMode: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/users/${userId}/sessions`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire('Todas las sesiones cerradas exitosamente.', {
                                        icon: 'success',
                                    }).then(() => {
                                        location.reload(); // Recargar la página para actualizar la lista
                                    });
                                } else {
                                    Swal.fire('Error al cerrar las sesiones.', {
                                        icon: 'error',
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Error al cerrar las sesiones.', {
                                    icon: 'error',
                                });
                            });
                    }
                });
            }
        </script>
    @endpush

</x-app-layout>
