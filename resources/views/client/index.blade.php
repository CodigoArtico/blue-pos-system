<x-app-layout>

    @section('title', 'clientes')

    @push('css')
    @endpush

    <style>
        .disabled {
            opacity: 0.5;
            /* Esto hará que el botón tenga una opacidad del 50% */
            cursor: not-allowed;
            /* Cambia el cursor para indicar que el botón no es interactivo */
            /* Otros estilos que desees aplicar para indicar que el botón está desactivado */
        }
    </style>
    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Clientes <i class="fa-solid fa-users fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Clientes</li>
        </ol>
        <div class="card mb-4 table-responsive">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-cliente')
                    <div class="mb-2">
                        <a href="#" class="btn btn-dark round icon" data-bs-toggle="modal"
                            data-bs-target="#crearClienteModal">
                            <i class="fas fa-plus"></i>
                        </a>
                        <button type="button" class="btn icon btn-success" data-bs-toggle="modal"
                            data-bs-target="#inlineForm">
                            <a href="#"><i class="fa-solid fa-chalkboard-user" style="color:#000000"></i></a>
                        </button>
                    </div>
                @endcan
                <div class="card-header font-semibold" style="color:black">
                    <i class="fas fa-table me-1"></i>
                    Registros
                </div>
                <div class="d-flex ">
                    <i data-feather="download"></i>
                </div>
            </div>
            <div class="card-body px-1 pb-1 table-responsive">
                <table class='table mb-0 table-responsive table-hover' id="table1">
                    <thead>
                        <tr>
                            <th>Razon Social</th>
                            <th>Dirección</th>
                            <th>E.Sunat/Cond.</th>
                            <th>Documento</th>
                            <th>Tipo de persona</th>
                            <th>Estado</th>
                            <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $item)
                            <tr>
                                <td>
                                    <strong style="color:black">{{ $item->persona->razon_social }}</strong>
                                </td>
                                <td>
                                    <i class="fa-solid fa-location-dot" style="color:black"></i>
                                    {{ $item->persona->direccion }}
                                </td>
                                <td>
                                    <p class="fw-bold mb-1" style="color:#000000">
                                        {{ $item->persona->estado_sunat }}</p>
                                    <p class="text-muted mb-0 fw-bold">{{ $item->persona->condicion }}</p>
                                </td>
                                <td>
                                    <p class="fw-bold mb-1" style="color:#000000">
                                        {{ $item->persona->documento->tipo_documento }}</p>
                                    <p class="text-muted mb-0 fw-bold">{{ $item->persona->numero_documento }}</p>
                                </td>

                                <td>
                                    @if ($item->persona->tipo_persona == 'natural')
                                        <i class="fa-solid fa-person" style="color:black"></i>
                                        {{ $item->persona->tipo_persona }}
                                    @else
                                        <i class="fa-solid fa-people-line" style="color:black"></i>
                                        {{ $item->persona->tipo_persona }}
                                    @endif
                                </td>
                                <td>
                                    @if ($item->persona->estado == 1)
                                        <i class="fa-solid fa-circle-check fa-2xl" style="color: #16ca76;"></i>
                                    @else
                                        <i class="fa-solid fa-circle-xmark fa-2xl" style="color: #a6a6a6;"></i>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <!-----Editar cliente--->
                                        @can('editar-cliente')
                                            @if ($item->persona->razon_social !== 'Publico General 777')
                                                @if ($item->persona->estado == 1)
                                                    <button type="button" class="btn icon" data-id="{{ $item->id }}">
                                                        <i class="fa-regular fa-pen-to-square" style="color: #52d4ff;"></i>
                                                    </button>
                                                @else
                                                    <form action="" method="get">
                                                        <button type="submit" class="btn icon disabled">
                                                            <i style="color: #52d4ff;">--</i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <button type="submit" class="btn icon" disabled>
                                                    <i style="color: #52d4ff;">--</i>
                                                </button>
                                            @endif
                                        @endcan
                                        <div class="px-1"></div>
                                        <!------Eliminar cliente---->
                                        @can('eliminar-cliente')
                                            @if ($item->persona->razon_social !== 'Publico General 777')
                                                <button type="button" class="btn icon btn" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal" data-id="{{ $item->id }}"
                                                    data-razon-social="{{ $item->persona->razon_social }}"
                                                    data-estado="{{ $item->persona->estado }}">
                                                    @if ($item->persona->estado == 1)
                                                        <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                    @else
                                                        <i class="fa-solid fa-arrow-rotate-right"
                                                            style="color: #ffa200;"></i>
                                                    @endif
                                                </button>
                                            @else
                                                <button type="submit" class="btn icon btn" disabled>
                                                    <i style="color: #d10000;">--</i>
                                                </button>
                                            @endif
                                        @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @can('crear-cliente')
        <!-- Modal para crear Cliente -->
        <div class="modal fade" id="crearClienteModal" tabindex="-1" aria-labelledby="crearClienteLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-crear-cliente" class="cliente-form" data-action="create">
                            @csrf
                            <div class="row">
                                <!-- Tipo de persona -->
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_persona" class="form-label">Tipo de cliente:</label>
                                    <select required class="form-select tipo-persona-select" name="tipo_persona"
                                        id="tipo_persona">
                                        <option value="" selected disabled>Seleccione una opción</option>
                                        <option value="natural">Persona natural</option>
                                        <option value="juridica">Persona jurídica</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-tipo_persona"></div>
                                </div>

                                <!-- Tipo de documento -->
                                <div class="col-md-6 mb-3">
                                    <label for="documento_id" class="form-label">Tipo de documento:</label>
                                    <select required class="form-select" name="documento_id" id="documento_id">
                                        <option value="" selected disabled>Seleccione una opción</option>
                                        @foreach ($documentos as $item)
                                            <option value="{{ $item->id }}">{{ $item->tipo_documento }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-documento_id"></div>
                                </div>

                                <!-- Razón social -->
                                <div class="col-md-6 mb-3" id="box-razon-social">
                                    <label for="razon_social" class="form-label">Nombres y apellidos:</label>
                                    <input required type="text" name="razon_social" id="razon_social"
                                        class="form-control" value="{{ old('razon_social') }}">
                                    <div class="invalid-feedback" id="error-razon_social"></div>
                                </div>

                                <!-- Número de documento -->
                                <div class="col-md-6 mb-3">
                                    <label for="numero_documento" class="form-label">Número de documento:</label>
                                    <input required type="text" name="numero_documento" id="numero_documento"
                                        class="form-control" value="{{ old('numero_documento') }}">
                                    <div class="invalid-feedback" id="error-numero_documento"></div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-md-6 mb-3">
                                    <label for="direccion" class="form-label">Dirección:</label>
                                    <input required type="text" name="direccion" id="direccion" class="form-control"
                                        value="{{ old('direccion') }}">
                                    <div class="invalid-feedback" id="error-direccion"></div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk me-2"></i>Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    @can('editar-cliente')
        <!-- Modal para editar Cliente -->
        <div class="modal fade" id="editarClienteModal" tabindex="-1" aria-labelledby="editarClienteLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-cliente" class="cliente-form" data-action="edit">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="cliente_id" id="cliente_id">
                            <div class="row">
                                <!-- Tipo de persona -->
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tipo_persona" class="form-label">Tipo de cliente:</label>
                                    <select required class="form-select tipo-persona-select" name="tipo_persona"
                                        id="edit_tipo_persona">
                                        <option value="" selected disabled>Seleccione una opción</option>
                                        <option value="natural">Persona natural</option>
                                        <option value="juridica">Persona jurídica</option>
                                    </select>
                                    <div class="invalid-feedback" id="error-edit-tipo_persona"></div>
                                </div>

                                <!-- Tipo de documento -->
                                <div class="col-md-6 mb-3">
                                    <label for="edit_documento_id" class="form-label">Tipo de documento:</label>
                                    <select required class="form-select" name="documento_id" id="edit_documento_id">
                                        <option value="" selected disabled>Seleccione una opción</option>
                                        @foreach ($documentos as $item)
                                            <option value="{{ $item->id }}">{{ $item->tipo_documento }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-edit-documento_id"></div>
                                </div>

                                <!-- Razón social -->
                                <div class="col-md-6 mb-3" id="edit_box_razon_social">
                                    <label for="edit_razon_social" class="form-label">Nombres y apellidos:</label>
                                    <input required type="text" name="razon_social" id="edit_razon_social"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-edit-razon_social"></div>
                                </div>

                                <!-- Número de documento -->
                                <div class="col-md-6 mb-3">
                                    <label for="edit_numero_documento" class="form-label">Número de documento:</label>
                                    <input required type="text" name="numero_documento" id="edit_numero_documento"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-edit-numero_documento"></div>
                                </div>

                                <!-- Dirección -->
                                <div class="col-sm-12 mb-3">
                                    <label for="edit_direccion" class="form-label">Dirección:</label>
                                    <input required type="text" name="direccion" id="edit_direccion"
                                        class="form-control">
                                    <div class="invalid-feedback" id="error-edit-direccion"></div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk me-2"></i>Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    @can('eliminar-cliente')
        <!-- Modal de Confirmación (fuera del foreach) -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="confirmModalLabel">Confirmación</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres <span id="actionText"></span> al cliente <strong
                            id="razonSocialText"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmAction">Sí, <span
                                id="actionButtonText"></span></button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para actualizar la etiqueta de razón social
            function updateRazonSocialLabel(selectElement, labelSelector) {
                const tipo = selectElement.value;
                const label = document.querySelector(labelSelector);
                if (tipo === 'natural') {
                    label.textContent = 'Nombres y apellidos:';
                } else if (tipo === 'juridica') {
                    label.textContent = 'Nombre de la empresa:';
                }
            }

            // Manejar cambios en los select de tipo de persona
            document.querySelectorAll('.tipo-persona-select').forEach(select => {
                select.addEventListener('change', function() {
                    const formAction = this.closest('form').getAttribute('data-action');
                    if (formAction === 'create') {
                        updateRazonSocialLabel(this, '#box-razon-social label');
                    } else if (formAction === 'edit') {
                        updateRazonSocialLabel(this, '#edit_box_razon_social label');
                    }
                });
            });

            // Función genérica para manejar la sumisión de formularios
            async function handleFormSubmit(event) {
                event.preventDefault();
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                const form = event.target;
                const formAction = form.getAttribute('data-action');
                clearValidationErrors(form);

                const formData = new FormData(form);
                const jsonData = Object.fromEntries(formData.entries());
                let url = '';
                let method = 'POST';
                let overrideMethod = '';

                if (formAction === 'create') {
                    url = '{{ route('clientes.store') }}';
                } else if (formAction === 'edit') {
                    const clienteId = document.getElementById('cliente_id').value;
                    url = `/clientes/${clienteId}`;
                    method = 'POST';
                    overrideMethod = 'PUT';
                }

                try {
                    const headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // Añadir este encabezado
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    };
                    if (overrideMethod) {
                        headers['X-HTTP-Method-Override'] = overrideMethod;
                    }

                    const response = await fetch(url, {
                        method: method,
                        headers: headers,
                        body: JSON.stringify(jsonData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (formAction === 'create') {
                            $('#crearClienteModal').modal('hide');
                        } else if (formAction === 'edit') {
                            $('#editarClienteModal').modal('hide');
                        }
                        mostrarToast('success', data.message);
                        reproducirSonidoSuccess();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        if (data.errors) {
                            reproducirSonidoAlert();
                            displayValidationErrors(form, data.errors);
                        } else {
                            mostrarToast('error', data.message || 'Ocurrió un error.');
                            reproducirSonidoAlert();
                        }
                    }
                } catch (error) {
                    console.error('Error en handleFormSubmit:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                    reproducirSonidoAlert();
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            }

            // Asignar manejador de eventos a todos los formularios de cliente
            document.querySelectorAll('.cliente-form').forEach(form => {
                form.addEventListener('submit', handleFormSubmit);
            });

            // Función genérica para mostrar errores de validación
            function displayValidationErrors(form, errors) {
                for (const [field, messages] of Object.entries(errors)) {
                    const errorDiv = form.querySelector(`#error-${field}`) || form.querySelector(
                        `#error-edit-${field}`);
                    const inputField = form.querySelector(`[name="${field}"]`);

                    if (errorDiv && inputField) {
                        errorDiv.textContent = messages[0];
                        inputField.classList.add('is-invalid');
                    }

                    // Verificar si el error está relacionado con 'numero_documento'
                    if (field === 'numero_documento') {
                        mostrarToast('error', messages[0]);
                    }
                }
            }

            // Función genérica para limpiar errores de validación
            function clearValidationErrors(form) {
                const errorDivs = form.querySelectorAll('.invalid-feedback');
                errorDivs.forEach(div => div.textContent = '');
                const invalidFields = form.querySelectorAll('.is-invalid');
                invalidFields.forEach(field => field.classList.remove('is-invalid'));
            }

            // **Actualización Aquí: Seleccionar Botones de Edición sin la Clase `btn-edit`**
            // Delegar el evento click para los botones de edición de cliente
            $('#table1').on('click', 'button.btn.icon:not([data-bs-toggle])', async function() {
                const clienteId = $(this).data('id'); // Usamos jQuery para obtener el ID del cliente

                // Limpia errores previos
                const editForm = document.getElementById('form-editar-cliente');
                clearValidationErrors(editForm);
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                // Obtiene los datos del cliente
                try {
                    const response = await fetch(`/clientes/${clienteId}/edit`);
                    const data = await response.json();

                    if (data.success) {
                        // Rellena el formulario con los datos del cliente
                        document.getElementById('cliente_id').value = data.cliente.id;
                        document.getElementById('edit_tipo_persona').value = data.cliente.persona
                            .tipo_persona;
                        document.getElementById('edit_documento_id').value = data.cliente.persona
                            .documento_id;
                        document.getElementById('edit_razon_social').value = data.cliente.persona
                            .razon_social;
                        document.getElementById('edit_numero_documento').value = data.cliente.persona
                            .numero_documento;
                        document.getElementById('edit_direccion').value = data.cliente.persona
                            .direccion;

                        // Cambia la etiqueta de razón social según el tipo de persona
                        updateRazonSocialLabel(document.getElementById('edit_tipo_persona'),
                            '#edit_box_razon_social label');

                        // Muestra el modal
                        $('#editarClienteModal').modal('show');
                    } else {
                        mostrarToast('error', data.message ||
                            'Error al obtener los datos del cliente.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            });

            // Eliminar o Restaurar cliente
            $(document).ready(function() {
                // Manejar la apertura del modal
                $('#table1').on('click', 'button[data-bs-target="#confirmModal"]', function() {
                    const id = $(this).data('id');
                    const razonSocial = $(this).data('razon-social');
                    const estado = $(this).data('estado');

                    // Determinar el texto según el estado
                    const action = estado == 1 ? 'eliminar' : 'restaurar';
                    const actionButtonText = estado == 1 ? 'Eliminar' : 'Restaurar';

                    // Rellenar el modal con los datos del cliente
                    $('#actionText').text(action);
                    $('#razonSocialText').text(razonSocial);
                    $('#actionButtonText').text(actionButtonText);

                    // Cambiar el color del botón de acción según el estado
                    $('#confirmAction').removeClass('btn-danger btn-warning').addClass(estado == 1 ?
                        'btn-danger' : 'btn-warning');

                    // Guardar el ID del cliente en el botón de confirmación
                    $('#confirmAction').data('id', id);
                });

                // Manejar la acción de eliminar/restaurar
                $('#confirmAction').on('click', async function() {
                    const id = $(this).data('id');
                    const updateUrl = '{{ url('clientes') }}/' + id;

                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();

                    try {
                        const response = await fetch(updateUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            $('#confirmModal').modal('hide');
                            mostrarToast('success', data.message);
                            reproducirSonidoSuccess();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarToast('error', data.message ||
                                'Ocurrió un error al procesar la solicitud.');
                            reproducirSonidoAlert();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        mostrarToast('error', 'Error de red. Por favor, intenta nuevamente.');
                        reproducirSonidoAlert();
                    } finally {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    }
                });
            });
        });
    </script>

    @push('js')
    @endpush

</x-app-layout>
