<x-app-layout>

    @section('title', 'roles')

    @push('css')
    @endpush


    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Roles <i class="fa-solid fa-person-circle-plus fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles</li>
        </ol>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-header font-semibold" style="color:#000000">
                    <i class="fas fa-table me-1"></i>
                    Registro Roles
                </div>

                @can('crear-roles')
                    <div class="mb-4">
                        <button type="button" class="btn btn-dark round icon" style="color: #d10000;"
                            data-bs-toggle="modal" data-bs-target="#crearRolModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                @endcan
            </div>

            <div class="card-body px-1 pb-1 table-responsive">
                <table id="table1" class="table table-hover table-striped table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>CD</th>
                            <th>ROL</th>
                            <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $item)
                            <tr>
                                <td>{{ Str::random(4) }}</td>
                                <td class="font-semibold" style="color:#000000">
                                    <i class="fa-regular fa-address-book"></i> {{ $item->name }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">

                                        @can('editar-roles')
                                            <button type="button" class="btn btn icon"
                                                onclick="openEditModal({{ $item->id }})">
                                                <i class="fa-regular fa-pen-to-square" style="color: #52d4ff;"></i>
                                            </button>
                                        @endcan
                                        <div class="px-1"></div>

                                        @can('eliminar-roles')
                                            @if ($item->name !== 'super_admin')
                                                <button type="button" class="btn btn icon"
                                                    onclick="openDeleteModal({{ $item->id }}, '{{ $item->name }}')">
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
    </div>

    @can('crear-roles')
        <!-- Modal para crear rol -->
        <div class="modal fade" id="crearRolModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Crear Nuevo Rol</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-crear-rol">
                            @csrf
                            <!-- Nombre de rol -->
                            <div class="row mb-4">
                                <label for="name" class="col-md-auto col-form-label">Nombre del rol:</label>
                                <div class="col-md-4">
                                    <input autocomplete="off" type="text" name="name" id="name"
                                        class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <small id="error-name" class="text-danger d-none"></small>
                                </div>
                            </div>
                            <!-- Botón para seleccionar/deseleccionar todos los permisos -->
                            <center>
                                <div class="form-check mb-3 px-2">
                                    <button type="button" id="select-all" class="btn btn-dark icon">Selec/Deselec</button>
                                </div>
                            </center>
                            <hr>
                            <!-- Permisos -->
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted">Permisos para el rol:</p>
                                    <div class="row">
                                        <!-- Roles que no tienen "super-" al inicio -->
                                        @foreach ($permisos as $item)
                                            @if (!str_starts_with($item->name, 'super-'))
                                                <div class="col-sm-3">
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox" name="permission[]" id="{{ $item->id }}"
                                                            class="form-check-input" value="{{ $item->id }}">
                                                        <label for="{{ $item->id }}"
                                                            class="form-check-label">{{ $item->name }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <!-- Roles que tienen "super-" al inicio -->
                                        @foreach ($permisos as $item)
                                            @if (str_starts_with($item->name, 'super-'))
                                                <div class="col-sm-3">
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox" name="permission[]" id="{{ $item->id }}"
                                                            class="form-check-input" value="{{ $item->id }}">
                                                        <label for="{{ $item->id }}"
                                                            class="form-check-label">{{ $item->name }}</label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <small id="error-permission" class="text-danger d-none"></small>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('editar-roles')
        <!-- Modal para editar rol -->
        <div class="modal fade" id="editarRolModal" tabindex="-1" aria-labelledby="editarRolLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editarRolLabel">Editar Rol</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-rol">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="role_id" id="role_id">
                            <!-- Nombre del rol -->
                            <div class="row mb-4">
                                <label for="name" class="col-md-auto col-form-label">Nombre del rol:</label>
                                <div class="col-md-4" id="role-name-container">
                                    <!-- El contenido se llenará dinámicamente -->
                                    <input type="text" name="name" id="edit_name" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <small id="edit_error-name" class="text-danger d-none"></small>
                                </div>
                            </div>
                            <!-- Permisos -->
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted">Permisos para el rol:</p>
                                    <div class="row" id="permissions-container"></div>
                                    <small id="edit_error-permission" class="text-danger d-none"></small>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('eliminar-roles')
        <!-- Modal de confirmación-->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="confirmModalLabel">Mensaje de confirmación</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar el rol "<span class="text-dark font-semibold" id="roleNameToDelete"></span>"?
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
            // seleccionar y deseleccionar 
            document.getElementById('select-all').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[name="permission[]"]');
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
            });

            function mostrarErrores(errors, prefix = '') {
                // Limpiar mensajes de error previos
                document.getElementById(prefix + 'error-name').classList.add('d-none');
                document.getElementById(prefix + 'error-permission').classList.add('d-none');

                // Mostrar mensajes de error específicos en los campos
                if (errors.name) {
                    document.getElementById(prefix + 'error-name').textContent = errors.name[0];
                    document.getElementById(prefix + 'error-name').classList.remove('d-none');
                }
                if (errors.permission) {
                    document.getElementById(prefix + 'error-permission').textContent = errors.permission[0];
                    document.getElementById(prefix + 'error-permission').classList.remove('d-none');
                }
            }

            // Función para mostrar errores con un prefijo opcional
            function mostrarErrores(errors, prefix = '') {
                // Limpia todos los mensajes de error con el prefijo
                const errorElements = document.querySelectorAll(`small[id^="${prefix}error-"]`);
                errorElements.forEach(el => {
                    el.classList.add('d-none');
                    el.textContent = '';
                });

                // Muestra los nuevos errores
                for (const [key, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(`${prefix}error-${key}`);
                    if (errorElement) {
                        errorElement.textContent = messages.join(' ');
                        errorElement.classList.remove('d-none');
                    }
                }
            }

            // Crear rol
            document.querySelector('#form-crear-rol').addEventListener('submit', async function(e) {
                e.preventDefault();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const formData = new FormData(this);
                const jsonData = {
                    name: formData.get('name'),
                    permission: formData.getAll('permission[]')
                };
                const storeUrl = '{{ route('roles.store') }}';

                try {
                    const response = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(jsonData)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        $('#crearRolModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else if (response.status === 422) {
                        reproducirSonidoAlert();
                        mostrarToast('error', data.message);

                        // Mostrar errores específicos
                        mostrarErrores(data.errors);
                    } else {
                        reproducirSonidoError();
                        mostrarToast('error', data.message || 'Ocurrió un error al registrar el rol.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            });

            // Editar rol 
            async function openEditModal(roleId) {
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                try {
                    const response = await fetch(`/roles/${roleId}/edit`, {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Error al obtener los datos del rol.');
                    }

                    const data = await response.json();

                    // Llenar el ID del rol
                    document.querySelector('#role_id').value = data.role.id;

                    // Manejar el campo de nombre del rol
                    const roleNameContainer = document.querySelector('#role-name-container');
                    roleNameContainer.innerHTML = ''; // Limpiar el contenido previo

                    if (data.role.name === 'super_admin') {
                        // Si el rol es super_admin, mostrar una etiqueta fija y un input oculto
                        roleNameContainer.innerHTML = `
                            <label class="form-control-plaintext text-black">super_admin</label>
                            <input type="hidden" name="name" id="edit_name" value="super_admin">
                        `;
                    } else {
                        // Si no, mostrar el campo de entrada editable
                        roleNameContainer.innerHTML = `
                        <input type="text" name="name" id="edit_name" class="form-control" value="${data.role.name}">
                    `;
                    }

                    // Renderizar los permisos y preseleccionar los existentes
                    const permissionsContainer = document.querySelector('#permissions-container');
                    permissionsContainer.innerHTML = ''; // Limpiar los permisos anteriores

                    // Separar los permisos en no-super y super
                    const nonSuperPermissions = data.permissions.filter(permission => !permission.name.startsWith(
                        'super-'));
                    const superPermissions = data.permissions.filter(permission => permission.name.startsWith('super-'));

                    // Renderizar los permisos no-super primero
                    if (nonSuperPermissions.length > 0) {
                        nonSuperPermissions.forEach(permission => {
                            const checked = data.rolePermissions.includes(permission.id) ? 'checked' : '';
                            permissionsContainer.innerHTML += `
                    <div class="col-sm-3">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="permission[]" id="perm_${permission.id}" 
                                   class="form-check-input" value="${permission.id}" ${checked}>
                            <label for="perm_${permission.id}" class="form-check-label">${permission.name}</label>
                        </div>
                    </div>`;
                        });
                    }

                    // Si hay permisos super, añadir un separador y luego listarlos
                    if (superPermissions.length > 0) {
                        permissionsContainer.innerHTML += `<div class="col-12"><hr class="my-3"></div>`;
                        superPermissions.forEach(permission => {
                            const checked = data.rolePermissions.includes(permission.id) ? 'checked' : '';
                            permissionsContainer.innerHTML += `
                            <div class="col-sm-3">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="permission[]" id="perm_${permission.id}" 
                                        class="form-check-input" value="${permission.id}" ${checked}>
                                    <label for="perm_${permission.id}" class="form-check-label">${permission.name}</label>
                                </div>
                            </div>`;
                        });
                    }

                    // Limpiar errores previos (opcional si mostrarErrores ya lo maneja)
                    mostrarErrores({}, 'edit_'); // Pasa un objeto vacío para limpiar errores

                    // Mostrar el modal de edición
                    $('#editarRolModal').modal('show');
                } catch (error) {
                    // console.error(error);
                    reproducirSonidoAlert();
                    mostrarToast('warning', 'Hubo un problema al cargar los datos del rol. Por favor, intenta de nuevo.')
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            }

            document.querySelector('#form-editar-rol').addEventListener('submit', async function(e) {
                e.preventDefault();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const formData = new FormData(this);
                const roleId = formData.get('role_id'); // ID del rol que se va a editar
                const jsonData = {
                    name: formData.get('name'),
                    permission: formData.getAll('permission[]')
                };

                try {
                    const response = await fetch(`/roles/${roleId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json', // Añadir este encabezado
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(jsonData)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        $('#editarRolModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else if (response.status === 422) {
                        reproducirSonidoAlert();
                        mostrarToast('error', data.message);

                        // Mostrar errores específicos en el modal de edición
                        mostrarErrores(data.errors, 'edit_');
                    } else {
                        reproducirSonidoError();
                        mostrarToast('error', 'Ocurrió un error al actualizar el rol.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            });

            //eliminar rol
            let roleIdToDelete = null;
            let roleNameToDelete = null;

            function openDeleteModal(roleId, roleName) {
                roleIdToDelete = roleId;
                roleNameToDelete = roleName;

                // Mostrar el nombre del rol en el modal
                document.getElementById('roleNameToDelete').innerText = roleNameToDelete;

                // Mostrar el modal
                $('#confirmModal').modal('show');
            }

            document.querySelector('#confirmDeleteBtn').addEventListener('click', async () => {
                if (roleIdToDelete) {
                    deleteRole(roleIdToDelete);
                }
            });

            async function deleteRole(roleId) {
                const deleteUrl = `/roles/${roleId}`;
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();

                try {
                    const response = await fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        $('#confirmModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        reproducirSonidoAlert();
                        mostrarToast('error', data.message || 'Ocurrió un error al eliminar el rol.');
                    }
                } catch (error) {
                    reproducirSonidoError();
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            }
        </script>
    @endpush

</x-app-layout>
