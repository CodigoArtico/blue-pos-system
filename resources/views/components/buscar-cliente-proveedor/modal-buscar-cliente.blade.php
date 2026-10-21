<style>
    /* Estilos para la superposición del spinner */
    .spinner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(204, 204, 204, 0.541);
        /* Fondo semitransparente */
        display: none;
        /* Oculto por defecto */
        justify-content: center;
        align-items: center;
        z-index: 1051;
        /* Asegúrate de que tenga un z-index mayor que el del modal */
        backdrop-filter: blur(2px);
        /* Efecto de desenfoque en el fondo */
    }

    /* Clase activa que hace visible el spinner */
    .spinner-overlay.active {
        display: flex;
    }
</style>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        // Función para mostrar el spinner overlay
        function showSpinner() {
            $('#spinnerOverlay').addClass('active');
        }

        // Función para ocultar el spinner overlay
        function hideSpinner() {
            $('#spinnerOverlay').removeClass('active');
        }

        // Mostrar/ocultar formularios según el tipo de documento
        $('#tipoDocumento').change(function() {
            var seleccion = $(this).val();
            if (seleccion === 'dni') {
                $('#formularioDNI').show();
                $('#formularioRUC').hide();
            } else if (seleccion === 'ruc') {
                $('#formularioRUC').show();
                $('#formularioDNI').hide();
            }
        });

        // Manejar búsqueda de DNI
        $('#buscarDNI').click(function() {
            var dni = $('#documentoDNI').val().trim();

            if (dni.length !== 8 || !/^\d+$/.test(dni)) {
                reproducirSonidoAlert();
                mostrarToast('warning', 'Por favor, ingresa un DNI válido de 8 dígitos.');
                return;
            }

            // Deshabilitar el botón y mostrar el spinner
            $('#buscarDNI').prop('disabled', true);
            showSpinner();

            $.ajax({
                url: '/consulta/dni', // Asegúrate de que la URL sea correcta
                method: 'POST',
                data: JSON.stringify({
                    dato: dni
                }),
                contentType: 'application/json',
                success: function(response) {
                    if (!response.error) {
                        $('#numeroDocumentoDNI').val(response.numeroDocumento || 'N/A');
                        $('#nombreDNI').val(response.nombre || 'N/A');
                        $('#tipoDocumento_Sunat').val(response.tipoDocumento || 'N/A');
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    reproducirSonidoAlert();
                    mostrarToast('warning', 'No válido. ' + xhr.responseJSON.message);
                },
                complete: function() {
                    // Rehabilitar el botón y ocultar el spinner
                    $('#buscarDNI').prop('disabled', false);
                    hideSpinner();
                }
            });
        });

        // Manejar búsqueda de RUC
        $('#buscarRUC').click(function() {
            var ruc = $('#documentoRUC').val().trim();

            if (ruc.length !== 11 || !/^\d+$/.test(ruc)) {
                reproducirSonidoAlert();
                mostrarToast('warning', 'Por favor, ingresa un RUC válido de 11 dígitos.');
                return;
            }

            // Deshabilitar el botón y mostrar el spinner
            $('#buscarRUC').prop('disabled', true);
            showSpinner();

            $.ajax({
                url: '/consulta/ruc', // Asegúrate de que la URL sea correcta
                method: 'POST',
                data: JSON.stringify({
                    dato: ruc
                }),
                contentType: 'application/json',
                success: function(response) {
                    if (!response.error) {
                        $('#numeroDocumentoRUC').val(response.numeroDocumento || 'N/A');
                        $('#razonSocialRUC').val(response.razonSocial || 'N/A');
                        $('#condicionRUC').val(response.condicion || 'N/A');
                        $('#estadoRUC').val(response.estado || 'N/A');
                        $('#direccionRUC').val(response.direccion || 'N/A');

                        $('#departamento').val(response.departamento || 'N/A');
                        $('#distrito').val(response.distrito || 'N/A');
                        $('#provincia').val(response.provincia || 'N/A');
                        $('#tipoDocumento_Sunat').val(response.tipoDocumento || 'N/A');
                        $('#ubigeo').val(response.ubigeo || 'N/A');
                        $('#viaNombre').val(response.viaNombre || 'N/A');
                        $('#viaTipo').val(response.viaTipo || 'N/A');
                        $('#zonaTipo').val(response.zonaTipo || 'N/A');
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    reproducirSonidoAlert();
                    mostrarToast('warning', 'No válido. ' + xhr.responseJSON.message);
                },
                complete: function() {
                    // Rehabilitar el botón y ocultar el spinner
                    $('#buscarRUC').prop('disabled', false);
                    hideSpinner();
                }
            });
        });

        // Manejar guardar cliente
        $('#guardarDatos').click(function() {
            var seleccion = $('#tipoDocumento').val();
            var tipo_persona, razon_social, direccion, documento_id, numero_documento;

            if (seleccion === 'dni') {
                razon_social = $('#nombreDNI').val();
                direccion = '-'; // Usar null en lugar de '*******'
                tipo_persona = 'natural';
                documento_id = 1;
                numero_documento = $('#numeroDocumentoDNI').val();
                tipoDocumento_Sunat = $('#tipoDocumento_Sunat').val();
            } else if (seleccion === 'ruc') {
                razon_social = $('#razonSocialRUC').val();
                direccion = $('#direccionRUC').val();
                tipo_persona = 'juridica';
                documento_id = 3;
                numero_documento = $('#numeroDocumentoRUC').val();

                condicionRUC = $('#condicionRUC').val();
                estadoRUC = $('#estadoRUC').val();
                departamento = $('#departamento').val();
                distrito = $('#distrito').val();
                provincia = $('#provincia').val();
                tipoDocumento_Sunat = $('#tipoDocumento_Sunat').val();
                ubigeo = $('#ubigeo').val();
                viaNombre = $('#viaNombre').val();
                viaTipo = $('#viaTipo').val();
                zonaTipo = $('#zonaTipo').val();
            }

            // Validar que los campos necesarios no estén vacíos
            if (!razon_social || !numero_documento) {
                reproducirSonidoAlert();
                mostrarToast('warning',
                    'Por favor, completa todos los campos necesarios antes de guardar.');
                return;
            }

            // Crear objeto con los datos a enviar
            var dataToSend = {
                tipo_persona: tipo_persona,
                razon_social: razon_social,
                direccion: direccion,
                documento_id: documento_id,
                numero_documento: numero_documento,

                condicionRUC: condicionRUC,
                estadoRUC: estadoRUC,
                departamento: departamento,
                distrito: distrito,
                provincia: provincia,
                tipoDocumento_Sunat: tipoDocumento_Sunat,
                ubigeo: ubigeo,
                viaNombre: viaNombre,
                viaTipo: viaTipo,
                zonaTipo: zonaTipo
            };
            // Validar y reemplazar campos vacíos con '-'
            for (var key in dataToSend) {
                if (Array.isArray(dataToSend[key]) && dataToSend[key].length === 0) {
                    dataToSend[key] = '-';
                } else if (dataToSend[key] === '' || dataToSend[key] === null || dataToSend[key] ===
                    undefined) {
                    dataToSend[key] = '-';
                }
            }

            // Determinar la URL a la que se enviarán los datos
            var urlToSend =
                (window.location.pathname === '/clientes' || window.location.pathname ===
                    '/ventas/create') ? '/guardar-cliente' :
                window.location.pathname === '/proveedores' ? '/guardar-proveedor' :
                window.location.pathname === '/empresas' ? '/empresas' :
                '/guardar'; // Ruta predeterminada o de fallback

            // Enviar los datos al servidor
            $.ajax({
                url: urlToSend, // Usar la URL dinámica según la condición
                method: 'POST',
                data: JSON.stringify(dataToSend),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        reproducirSonidoSuccess();
                        mostrarToast('success', response.message);

                        // Cerrar el modal
                        $('#inlineForm').modal('hide');

                        //si estamos en la ruta ventas/create que cargue los datos en el select2 
                        if (window.location.pathname === '/ventas/create') {

                            // Obtener los datos del nuevo cliente
                            var nuevoCliente = response.data;
                            var razonSocial = nuevoCliente.persona.razon_social;
                            var numeroDocumento = nuevoCliente.persona
                                .numero_documento; // Obtener numero_documento
                            var clienteId = nuevoCliente.id
                                .toString(); // Asegurarse de que sea string

                            // Crear una nueva opción con numero_documento y razon_social
                            var optionText = numeroDocumento + '-' + razonSocial;
                            var newOption = new Option(optionText, clienteId, true, true);
                            $('#cliente_id').append(newOption).trigger('change');

                            // Limpiar los campos del formulario en el modal si es necesario
                            $('#formularioDNI input, #formularioRUC input').val('');
                            $('#tipoDocumento').val('dni').trigger('change');

                        }

                        // Verificar si estamos en /clientes o /proveedores y recargar la página en esos casos
                        if (window.location.pathname === '/clientes' || window.location
                            .pathname === '/proveedores' || window.location
                            .pathname === '/empresas') {
                            setTimeout(function() {
                                location.reload();
                            }, 1500); // 1500 milisegundos = 1.5 segundos

                        }
                    } else {
                        reproducirSonidoAlert();
                        mostrarToast('error', response.message ||
                            'Ocurrió un error al guardar el cliente.');
                    }
                },
                error: function(xhr) {
                    reproducirSonidoAlert();
                    var errorMessage = 'Ocurrió un error al guardar el cliente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    mostrarToast('error', errorMessage);
                }
            });
        });

    });
</script>

@php
    // Definir clases por defecto
    $headerClass = 'bg-primary text-white';
    $buttonClass = 'btn-primary';

    // Cambiar clases según la ruta
    if (Request::is('clientes')) {
        $headerClass = 'bg-info text-white';
        $buttonClass = 'btn-info';
    } elseif (Request::is('ventas/create')) {
        $headerClass = 'bg-dark text-white';
        $buttonClass = 'btn-dark';
    } elseif (Request::is('proveedores')) {
        $headerClass = 'bg-light text-white';
        $buttonClass = 'btn-light';
    } elseif (Request::is('empresas')) {
        $headerClass = 'bg-primary text-white';
        $buttonClass = 'btn-primary';
    }
@endphp

<div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content shadow-lg position-relative">
            <!-- Spinner de carga con overlay -->
            <div id="spinnerOverlay" class="spinner-overlay">
                <div class="loading-spinner"></div>
            </div>

            <!-- Encabezado Dinámico -->
            <div class="modal-header {{ $headerClass }}">
                @if (Request::is('clientes') || Request::is('ventas/create'))
                    Nuevo Cliente
                @elseif (Request::is('proveedores'))
                    Nuevo Proveedor
                @elseif (Request::is('empresas'))
                    Mi Negocio
                @else
                    Nuevo Registro
                @endif
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body text-center">
                <br>
                <div class="container">
                    @if (Request::is('empresas'))
                        <h3 class="text-center mt-0">Consulta Negocio/Empresa</h3>
                    @else
                        <h3 class="text-center mt-0">Consulta de Documentos</h3>
                    @endif
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <select id="tipoDocumento" class="form-select">
                                    <option value="dni">DNI</option>
                                    <option value="ruc">RUC</option>
                                </select>
                            </div>
                            <div id="formularioDNI">
                                <h5 class="text-center mb-3">Consulta de DNI</h5>
                                <div class="input-group mb-3">
                                    <input type="number" id="documentoDNI" class="form-control"
                                        placeholder="Ingrese su DNI">
                                    <button class="btn {{ $buttonClass }}" type="button"
                                        id="buscarDNI">Buscar</button>
                                </div>
                                <input type="text" id="numeroDocumentoDNI" class="form-control mb-2"
                                    placeholder="Número de Documento" disabled>
                                <input type="text" id="nombreDNI" class="form-control mb-2" placeholder="Nombre"
                                    disabled>
                                <input type="hidden" id="tipoDocumento_Sunat" class="form-control mb-2"
                                    placeholder="Tipo Documento" disabled>
                            </div>
                            <div id="formularioRUC" style="display: none;">
                                <h5 class="text-center mb-3">Consulta de RUC</h5>
                                <div class="input-group mb-3">
                                    <input type="number" id="documentoRUC" class="form-control"
                                        placeholder="Ingrese su RUC">
                                    <button class="btn {{ $buttonClass }}" type="button"
                                        id="buscarRUC">Buscar</button>
                                </div>
                                <input type="text" id="numeroDocumentoRUC" class="form-control mb-2"
                                    placeholder="Número de Documento" disabled>
                                <input type="text" id="razonSocialRUC" class="form-control mb-2"
                                    placeholder="Razón Social" disabled>
                                <input type="text" id="condicionRUC" class="form-control mb-2"
                                    placeholder="Condición" disabled>
                                <input type="text" id="estadoRUC" class="form-control mb-2" placeholder="Estado"
                                    disabled>
                                <input type="text" id="direccionRUC" class="form-control mb-2"
                                    placeholder="Dirección" disabled>

                                <input type="hidden" id="departamento" class="form-control mb-2"
                                    placeholder="Departamento" disabled>
                                <input type="hidden" id="distrito" class="form-control mb-2" placeholder="Distrito"
                                    disabled>
                                <input type="hidden" id="provincia" class="form-control mb-2" placeholder="Provincia"
                                    disabled>
                                <input type="hidden" id="tipoDocumento_Sunat" class="form-control mb-2"
                                    placeholder="Tipo Documento" disabled>
                                <input type="hidden" id="ubigeo" class="form-control mb-2" placeholder="Ubigeo"
                                    disabled>
                                <input type="hidden" id="viaNombre" class="form-control mb-2"
                                    placeholder="Via Nombre" disabled>
                                <input type="hidden" id="viaTipo" class="form-control mb-2"
                                    placeholder="Via Tipo" disabled>
                                <input type="hidden" id="zonaTipo" class="form-control mb-2"
                                    placeholder="Zona Tipo" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn {{ $buttonClass }}" id="guardarDatos">Guardar</button>
            </div>
        </div>
    </div>
</div>
