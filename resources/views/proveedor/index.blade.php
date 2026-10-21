<x-app-layout>

    @section('title', 'proveedores')

    @push('css')
    @endpush

    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Proveedores <i class="fa-solid fa-truck fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Proveedores</li>
        </ol>

        <div class="card mb-4 table-responsive">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-proveedor')
                    <div class="mb-2">
                        <button class="btn btn-dark round icon" data-bs-toggle="modal"
                            data-bs-target="#crearProveedorModal">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn icon btn-success" data-bs-toggle="modal"
                            data-bs-target="#inlineForm">
                            <a href="#"><i class="fa-solid fa-chalkboard-user" style="color:#000000"></i></a>
                        </button>
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
            <div class="card-body px-1 pb-1 table-responsive">
                <table class='table table-hover table-bordered mb-0' id="table1">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>E.Sunat/Cond.</th>
                            <th>Documento</th>
                            <th>Tipo de persona</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proveedores as $item)
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
                                        <!-----Editar proveedore--->
                                        @can('editar-proveedor')
                                            @if ($item->persona->razon_social !== 'Proveedor General')
                                                @if ($item->persona->estado == 1)
                                                    <button type="button" class="btn icon modal-editar"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fa-regular fa-pen-to-square" style="color: #52d4ff;"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn icon disabled" disabled>
                                                        <i style="color: #52d4ff;">--</i>
                                                    </button>
                                                @endif
                                            @else
                                                <button type="submit" class="btn icon" disabled>
                                                    <i style="color: #52d4ff;">--</i>
                                                </button>
                                            @endif
                                        @endcan

                                        <div class="px-1"></div>

                                        <!------Eliminar proveedore---->
                                        @can('eliminar-proveedor')
                                            @if ($item->persona->razon_social !== 'Proveedor General')
                                                <button type="button" class="btn icon btn" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal"
                                                    data-id="{{ $item->id }}"
                                                    data-razon-social="{{ $item->persona->razon_social }}"
                                                    data-estado="{{ $item->persona->estado }}">
                                                    @if ($item->persona->estado == 1)
                                                        <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                    @else
                                                        <i class="fa-solid fa-arrow-rotate-right" style="color: #ffa200;"></i>
                                                    @endif
                                                </button>
                                            @else
                                                <button type="button" class="btn icon btn" disabled>
                                                    <i style="color: #d10000;">--</i>
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

    @can('crear-proveedor')
        <!-- Modal para Crear Proveedor -->
        <div class="modal fade" id="crearProveedorModal" tabindex="-1" aria-labelledby="crearProveedorLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-crear-proveedor" class="proveedor-form" data-action="create">
                            @csrf
                            <div class="row">
                                <!-- Tipo de persona -->
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_persona" class="form-label">Tipo de proveedor:</label>
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
                                        @foreach ($documentos as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->tipo_documento }}</option>
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

    @can('editar-proveedor')
        <!-- Modal para Editar Proveedor -->
        <div class="modal fade" id="editarProveedorModal" tabindex="-1" aria-labelledby="editarProveedorLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-proveedor" class="proveedor-form" data-action="edit">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="proveedor_id" id="proveedor_id">
                            <div class="row">
                                <!-- Tipo de persona -->
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tipo_persona" class="form-label">Tipo de proveedor:</label>
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
                                        @foreach ($documentos as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->tipo_documento }}</option>
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

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="confirmModalLabel">Confirmación</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres <span id="actionText"></span> al proveedor <strong id="proveedorNombre"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmAction">Sí, <span id="actionButtonText"></span></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el token CSRF desde la meta etiqueta
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                    url = '{{ route('proveedores.store') }}';
                } else if (formAction === 'edit') {
                    const proveedorId = document.getElementById('proveedor_id').value;
                    url = `/proveedores/${proveedorId}`;
                    method = 'POST';
                    overrideMethod = 'PUT';
                }

                try {
                    const headers = {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' // Asegura que la respuesta sea JSON
                    };
                    if (overrideMethod) {
                        headers['X-HTTP-Method-Override'] = overrideMethod;
                    }

                    const response = await fetch(url, {
                        method: method,
                        headers: headers,
                        body: JSON.stringify(jsonData)
                    });

                    // Manejar diferentes tipos de respuestas basadas en el código de estado HTTP
                    if (response.ok) { // 2xx
                        const data = await response.json();
                        if (data.success) {
                            if (formAction === 'create') {
                                $('#crearProveedorModal').modal('hide');
                            } else if (formAction === 'edit') {
                                $('#editarProveedorModal').modal('hide');
                            }
                            reproducirSonidoSuccess();
                            mostrarToast('success', data.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarToast('error', data.message || 'Ocurrió un error.');
                            reproducirSonidoAlert();
                        }
                    } else if (response.status === 422) { // Errores de validación
                        const data = await response.json();
                        if (data.errors) {
                            displayValidationErrors(form, data.errors);
                        } else {
                            reproducirSonidoAlert();
                            mostrarToast('error', 'Ocurrió un error de validación.');

                        }
                    } else { // Otros errores
                        const data = await response.json();
                        mostrarToast('error', data.message || 'Ocurrió un error.');
                        reproducirSonidoAlert();
                        // console.log(data.message);

                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                    reproducirSonidoAlert();
                } finally {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                }
            }

            // Asignar manejador de eventos a todos los formularios de proveedor
            document.querySelectorAll('.proveedor-form').forEach(form => {
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

            // Seleccionar Botones de Edición Específicos
            // Delegar el evento click para los botones de edición
            $('#table1').on('click', 'button.modal-editar[data-id]', async function() {
                const proveedorId = $(this).data('id'); // Usa jQuery para obtener el id

                // Limpia errores previos
                const editForm = document.getElementById('form-editar-proveedor');
                clearValidationErrors(editForm);
                // Mostrar el spinner al iniciar la solicitud
                showSpinner();
                // Obtiene los datos del proveedor
                try {
                    const response = await fetch(`/proveedores/${proveedorId}/edit`);
                    const data = await response.json();

                    if (data.success) {
                        const proveedor = data.proveedore;
                        // Rellena el formulario con los datos del proveedor
                        document.getElementById('proveedor_id').value = proveedor.id;
                        document.getElementById('edit_tipo_persona').value = proveedor.persona
                            .tipo_persona;
                        document.getElementById('edit_documento_id').value = proveedor.persona
                            .documento_id;
                        document.getElementById('edit_razon_social').value = proveedor.persona
                            .razon_social;
                        document.getElementById('edit_numero_documento').value = proveedor.persona
                            .numero_documento;
                        document.getElementById('edit_direccion').value = proveedor.persona.direccion;

                        // Cambia la etiqueta de razón social según el tipo de persona
                        updateRazonSocialLabel(document.getElementById('edit_tipo_persona'),
                            '#edit_box_razon_social label');

                        // Muestra el modal
                        $('#editarProveedorModal').modal('show');
                    } else {
                        mostrarToast('error', data.message ||
                            'Error al obtener los datos del proveedor.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                } finally {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                }
            });

            // Eliminar o Restaurar proveedor
            $(document).ready(function() {
                let proveedorIdToDelete = null;
                let proveedorEstado = null;

                // Manejar la apertura del modal
                $('#table1').on('click', 'button[data-bs-target="#confirmModal"]', function() {
                    proveedorIdToDelete = $(this).data('id'); // Obtener el ID del proveedor
                    const razonSocial = $(this).data('razon-social'); // Obtener la razón social
                    proveedorEstado = $(this).data('estado'); // Obtener el estado

                    // Determinar el texto según el estado
                    const action = proveedorEstado == 1 ? 'eliminar' : 'restaurar';
                    const actionButtonText = proveedorEstado == 1 ? 'Eliminar' : 'Restaurar';

                    // Rellenar el modal con los datos del proveedor
                    $('#actionText').text(action);
                    $('#proveedorNombre').text(razonSocial);
                    $('#actionButtonText').text(actionButtonText);

                    // Cambiar el color del botón de acción según el estado
                    $('#confirmAction').removeClass('btn-danger btn-warning').addClass(proveedorEstado == 1 ? 'btn-danger' : 'btn-warning');
                });

                // Manejar la acción de eliminar/restaurar
                $('#confirmAction').on('click', function() {
                    if (!proveedorIdToDelete) {
                        mostrarToast('error', 'ID de proveedor no encontrado.');
                        return;
                    }

                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();

                    // Realizar la solicitud AJAX para eliminar/restaurar
                    const updateUrl = `/proveedores/${proveedorIdToDelete}`;
                    const method = 'DELETE'; // Usar PUT para restaurar

                    $.ajax({
                        url: updateUrl,
                        type: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        success: function(response) {
                            $('#confirmModal').modal('hide'); // Cerrar el modal
                            reproducirSonidoSuccess();
                            mostrarToast('success', response.message);
                            setTimeout(() => location.reload(), 1500); // Recargar la página después de 1.5 segundos
                        },
                        error: function(xhr) {
                            let errorMsg = 'Ocurrió un error al procesar la solicitud.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            mostrarToast('error', errorMsg);
                        },
                        complete: function() {
                            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                            hideSpinner();
                            proveedorIdToDelete = null; // Resetear el ID después de la acción
                        }
                    });
                });
            });
        });
    </script>
    @push('js')
    @endpush

</x-app-layout>
