<x-app-layout>
    @section('title', 'Empresa')

    @push('css')
        <style>
            /* Puedes agregar estilos personalizados aquí */
        </style>
    @endpush

    <div class="main-content container mt-2">
        <div class="page-title text-center">
            <h1 class="text-dark">Empresa <i class="fa-solid fa-store fa-lg"></i></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Empresa</li>
                </ol>
            </nav>
        </div>

        <div class="card mt-2 shadow-sm">
            <div class="card-header bg-primary">
                <h3 class="mb-0 text-center" style="color:black">{{ $empresa->razon_social ?? 'No asignado' }}</h3>
            </div>
            <div class="card-body mt-1">
                @can('super-boton-obtener-sistema')
                    <H6 class="text-dark">
                        @include('components.obtener-sistema.obtener')
                    </H6>
                @endcan
                <!-- Información General de la Empresa -->
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong class="text-dark">DNI/RUC:</strong> <i class="fa-solid fa-id-card-clip"
                                style="color:black"></i> {{ $empresa->ruc_dni ?? 'No asignado' }}</p>
                    </div>
                    <div class="col-md-5">
                        <p><strong class="text-dark">Dirección:</strong>
                            <i class="fa-solid fa-map-location-dot" style="color:black"></i>
                            {{ $empresa->direccion_empresa ?? 'No asignado' }}
                        </p>
                    </div>
                    <div class="col-3">
                        <p><strong class="text-dark">Ubig:</strong>
                            <i class="fa-solid fa-arrow-up-1-9" style="color:black"></i>
                            {{ $empresa->ubigeo ?? 'No asignado' }}
                        </p>
                    </div>
                    <div class="col-1">
                        @can('configuracion-datos-empresa')
                            <button type="button" class="btn btn-outline-primary icon" data-bs-toggle="modal"
                                data-bs-target="#inlineForm">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        @endcan
                    </div>
                    <div class="col-md-4">
                        <p><strong class="text-dark">Depart:</strong>
                            <i class="fa-solid fa-magnifying-glass-location" style="color:black"></i>
                            {{ $empresa->departamento ?? 'No asignado' }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p><strong class="text-dark">Prov:</strong>
                            <i class="fa-solid fa-mountain-city" style="color:black"></i>
                            {{ $empresa->provincia ?? 'No asignado' }}
                        </p>
                    </div>
                    <div class="col-md-4">
                        <p><strong class="text-dark">Dist:</strong>
                            <i class="fa-solid fa-building-user" style="color:black"></i>
                            {{ $empresa->distrito ?? 'No asignado' }}
                        </p>
                    </div>
                </div>

                <!-- Detalles del Comprobante -->
                <div class="col-md-12">
                    <hr>
                    <h4 class="text-secondary text-center text-dark mb-4">Detalles del Comprobante/ Comercial <i
                            class="fa-solid fa-file-invoice fa-lg"></i></h4>
                </div>

                <div class="row mt-2">
                    <!-- Imagen del Comprobante -->
                    <div class="col-md-4 text-center">
                        <li class="text-dark fs-6 ">Tamaño Recomendado: 1500 x 700 px</li>
                        <img src="{{ asset($empresa->img_account ? 'storage/img_account_empresa/' . $empresa->img_account : 'images/banner2.png') }}"
                            alt="Logo de la empresa" class="img-thumbnail" width="200" height="100">
                    </div>

                    <!-- Datos del Comprobante -->
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong class="text-dark">Razon S. Comp:</strong>
                                    <i class="fa-solid fa-pen" style="color:black"></i>
                                    {{ $empresa->razon_social_comprobante ?? 'No asignado' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong class="text-dark">Teléfono:</strong>
                                    <i class="fa-solid fa-phone" style="color:black"></i>
                                    {{ $empresa->telefono_comprobante ?? 'No asignado' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong class="text-dark">Direcc - Compr:</strong>
                                    <i class="fa-solid fa-location-dot" style="color:black"></i>
                                    {{ $empresa->direccion_comprobante ?? 'No asignado' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong class="text-dark">Subtítulo:</strong>
                                    <i class="fa-regular fa-file-lines" style="color:black"></i>
                                    {{ $empresa->subtitulo_principal_comprobante ?? 'No asignado' }}
                                </p>
                            </div>
                            <div class="col-md-12">
                                <p><strong class="text-dark">Descripción:</strong>
                                    <i class="fa-solid fa-file-lines" style="color:black"></i>
                                    {{ $empresa->descripcion_aviso_comprobante ?? 'No asignado' }}
                                </p>
                            </div>
                            <div class="col-md-2">
                                <p><strong class="text-dark">Mi Ticket:</strong>
                                    {{-- Botón para abrir el modal --}}
                                    <button type="button" class="btn btn-outline-dark icon" data-bs-toggle="modal"
                                        data-bs-target="#modalTicket{{ $item->id }}">
                                        <i class="fa-solid fa-print"></i>
                                    </button>

                                    {{-- Incluir la vista parcial del modal --}}
                                    @include('venta.partials.ticket-venta', ['venta' => $item])
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong class="text-dark">Despedida:</strong>
                                    <i class="fa-solid fa-hands-clapping" style="color:black"></i>
                                    {{ $empresa->despedida_comprobante ?? 'No asignado' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @can('configuracion-datos-empresa')
                <div class="card-footer text-right">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#editarEmpresaModal">
                        Editar
                    </button>
                </div>
            @endcan
        </div>
    </div>

    <!-- Modal para editar Detalles del Comprobante y Certificado -->
    <div class="modal fade" id="editarEmpresaModal" tabindex="-1" aria-labelledby="editarEmpresaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-dark" id="editarEmpresaLabel">
                        <i class="fa-solid fa-edit me-2"></i>Editar Detalles del Comprobante y Certificado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarEmpresaForm" enctype="multipart/form-data">
                        @csrf
                        <div class="container-fluid">
                            <!-- Detalles del Comprobante -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <div class="card-header bg-light border-bottom border-primary">
                                            <h6 class="mb-0"><i class="fa-solid fa-file-invoice me-2"></i>Detalles
                                                del Comprobante</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="razon_social_comprobante" class="form-label">Nombre
                                                        Comprobante</label>
                                                    <input type="text" class="form-control"
                                                        id="razon_social_comprobante" name="razon_social_comprobante"
                                                        value="{{ $empresa->razon_social_comprobante ?? '' }}"
                                                        placeholder="Ingrese el nombre del comprobante">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="ruc" class="form-label">Ruc/DNI:</label>
                                                    <input type="text" class="form-control" id="ruc"
                                                        name="ruc" value="{{ $empresa->certificado->ruc ?? '' }}"
                                                        maxlength="11" pattern="\d{11}" required
                                                        title="Debes ingresar 11 dígitos"
                                                        placeholder="Ingrese el Ruc/DNI">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="telefono_comprobante"
                                                        class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control"
                                                        id="telefono_comprobante" name="telefono_comprobante"
                                                        value="{{ $empresa->telefono_comprobante ?? '' }}"
                                                        placeholder="Ingrese el teléfono">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="direccion_comprobante" class="form-label">Dirección
                                                        Comprobante</label>
                                                    <input type="text" class="form-control"
                                                        id="direccion_comprobante" name="direccion_comprobante"
                                                        value="{{ $empresa->direccion_comprobante ?? '' }}"
                                                        placeholder="Ingrese la dirección">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="subtitulo_principal_comprobante"
                                                        class="form-label">Subtítulo</label>
                                                    <input type="text" class="form-control"
                                                        id="subtitulo_principal_comprobante"
                                                        name="subtitulo_principal_comprobante"
                                                        value="{{ $empresa->subtitulo_principal_comprobante ?? '' }}"
                                                        placeholder="Ingrese el subtítulo">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="descripcion_aviso_comprobante"
                                                        class="form-label">Descripción</label>
                                                    <textarea class="form-control" id="descripcion_aviso_comprobante" name="descripcion_aviso_comprobante"
                                                        rows="3" placeholder="Ingrese la descripción">{{ $empresa->descripcion_aviso_comprobante ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="despedida_comprobante"
                                                        class="form-label">Despedida</label>
                                                    <input type="text" class="form-control"
                                                        id="despedida_comprobante" name="despedida_comprobante"
                                                        value="{{ $empresa->despedida_comprobante ?? '' }}"
                                                        placeholder="Ingrese la despedida">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="img_account" class="form-label">Imagen del
                                                        Comprobante <a href="#" class="text-primary">1500 x 700 px (recomendado)</a></label>
                                                    <input type="file" class="form-control" id="img_account"
                                                        name="img_account" accept="image/*">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="guardarCambios">
                        <i class="fa-solid fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                // Función para mostrar u ocultar el campo de contraseña del certificado
                $('#certificado_archivo').on('change', function() {
                    const fileExtension = $(this).val().split('.').pop().toLowerCase();
                    if (fileExtension === 'pfx') {
                        $('#certificado_password_field').show();
                    } else {
                        $('#certificado_password_field').hide();
                        $('#certificado_password').val('');
                    }
                });

                // Inicializar el estado al cargar la página
                $('#certificado_archivo').trigger('change');

                // Manejar el evento de guardar cambios
                // Manejar el evento de guardar cambios
                $('#guardarCambios').click(function(e) {
                    e.preventDefault();

                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();

                    // Crear un objeto FormData a partir del formulario
                    var formData = new FormData($('#editarEmpresaForm')[0]);

                    // Añadir el token CSRF
                    formData.append('_token', '{{ csrf_token() }}');

                    // Añadir el método PUT
                    formData.append('_method', 'PUT');

                    // Realizar la solicitud AJAX
                    $.ajax({
                            url: "{{ route('empresas.update', $empresa->id) }}",
                            type: 'POST', // Mantener como POST
                            data: formData,
                            contentType: false, // Importante para enviar multipart/form-data
                            processData: false, // Importante para no procesar los datos
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Cerrar el modal
                                    $('#editarEmpresaModal').modal('hide');

                                    reproducirSonidoSuccess();
                                    mostrarToast('success', response.message);
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1500); // 1500 milisegundos = 1.5 segundos
                                } else {
                                    // Mostrar errores de validación
                                    reproducirSonidoAlert();
                                    mostrarToast('error', 'Error al actualizar los datos.');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    // Manejar errores de validación
                                    var errors = xhr.responseJSON.errors;
                                    var errorMessages = '';
                                    $.each(errors, function(key, value) {
                                        errorMessages += value[0] + '\n';
                                    });
                                    reproducirSonidoAlert();
                                    mostrarToast('warning', errorMessages);
                                } else if (xhr.responseJSON && xhr.responseJSON.errors && xhr
                                    .responseJSON.errors.general) {
                                    // Mostrar mensaje de error específico
                                    var generalErrorMessage = xhr.responseJSON.errors.general[0];
                                    reproducirSonidoError();
                                    mostrarToast('error', generalErrorMessage);
                                    console.log(generalErrorMessage);
                                } else {
                                    // Manejar otros errores
                                    reproducirSonidoError();
                                    mostrarToast('error', 'Ocurrió un error al procesar la solicitud.');
                                }
                            }
                        })
                        .always(function() { // Usar .always en vez de .finally()
                            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                            hideSpinner(); // Llamada directa a la función hideSpinner
                        });
                });
            });
        </script>
    @endpush

</x-app-layout>
