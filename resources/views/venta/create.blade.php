<x-app-layout>

    @section('title', 'Realizar venta')
    @push('css')
        @include('venta.partials.create-css')
    @endpush

    <div class="container-fluid ml-8">
        <h1 class="mt-2 text-center text-dark">Realizar Venta <i class="fa-solid fa-cart-plus fa-lg"></i></h1>
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Menú</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
            <li class="breadcrumb-item active">Realizar Venta</li>
        </ol>
        <div class="text-dark alert alert-secondary alert-dismissible fade show text-center page-title">
            <span>Recuerda que para emitir Boletas o Facturas necesitas configurar la seccion de <a
                    href="{{ route('empresas.index') }}" class="text-dark"><strong>Empresa</strong></a>.
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </span>
        </div>
        <form id="formVenta" action="{{ route('ventas.store') }}" method="post">
            @csrf
            <div class="container-fluid">

                {{-- AQUI INCLUIMOS LA VENTA CREADA CON ENLACE DEL TICKET  --}}
                @if (session('venta'))
                    @php
                        $venta = session('venta'); // Recuperar la instancia completa de la venta
                    @endphp

                    <div class="text-dark alert alert-info alert-dismissible fade show text-center" role="alert">
                        <span style="margin-left: 5px; margin-right: 5px;">Ult. Venta Realizada: </span>

                        {{-- Botón para abrir el modal --}}
                        <button type="button" class="btn btn-outline-dark icon" data-bs-toggle="modal"
                            data-bs-target="#modalTicket{{ $venta->id }}">
                            <i class="fa-solid fa-print"></i>
                        </button>
                        @include('venta.partials.ticket-venta', ['venta' => $venta])
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                @endif
                <div class="row gy-4">
                    {{-- Si existe la orden, enviamos el código de la orden --}}
                    @if (request()->has('orden'))
                        <input type="hidden" name="codigoOrden" value="{{ request()->orden }}">
                    @endif
                    {{-- Si existe una mesa, enviamos el código de la mesa --}}
                    @if (request()->has('mesa'))
                        <input type="hidden" name="codigoMesa" value="{{ request()->mesa }}">
                    @endif
                    <!-----Venta---->
                    <div class="col-xl-4">
                        <div class="text-white bg-dark p-1 text-center mb-1" style="border-radius: 15px;">
                            Datos generales
                        </div>
                        <div class="p-2 border border-info" style="border-radius: 15px;">
                            <div class="row gy-1">
                                {{-- script para manejar serie, impuesto de acuerdo al comprobante  --}}
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const comprobanteSelect = document.getElementById('comprobante_id');
                                        const serieInput = document.getElementById('serie');
                                        const impuestoInput = document.getElementById('igv_impuesto');

                                        function actualizarCampos() {
                                            const selectedOption = comprobanteSelect.options[comprobanteSelect.selectedIndex];
                                            const serie = selectedOption.getAttribute('data-serie') || '';
                                            const impuesto = selectedOption.getAttribute('data-impuesto') || '0%';

                                            serieInput.value = serie;
                                            impuestoInput.value = impuesto;

                                            // Añadido: Recalcular y mostrar totales cada vez que se actualizan los campos
                                            recalcularTotales();
                                            mostrarTotales();
                                            calcularVuelto(); // Actual
                                        }

                                        // Actualizar campos al cargar la página
                                        actualizarCampos();

                                        // Escuchar cambios en el select
                                        comprobanteSelect.addEventListener('change', actualizarCampos);

                                        // Movido: Recalcular y mostrar totales al cargar la página
                                        recalcularTotales();
                                        mostrarTotales();
                                    });
                                </script>

                                <!-- Tipo de comprobante -->
                                <div class="col-12 mb-1">
                                    {{-- <label for="comprobante_id" class="form-label" style="color: black;">Comprobante:</label> --}}
                                    <div class="input-group">
                                        {{-- <span class="input-group-text" id="comprobante-addon">
                                            <i class="bi bi-receipt"></i> <!-- Icono de Bootstrap Icons -->
                                        </span> --}}
                                        <select name="comprobante_id" id="comprobante_id" class="form-select"
                                            aria-describedby="comprobante-addon" title="Selecciona">
                                            @foreach ($comprobantes as $index => $item)
                                                @php
                                                    // Determinar el impuesto según el tipo de comprobante
                                                    if (
                                                        $item->tipo_comprobante === 'FACTURA' ||
                                                        $item->tipo_comprobante === 'BOLETA'
                                                    ) {
                                                        $impuesto = '18%';
                                                    } else {
                                                        $impuesto = '0%';
                                                    }

                                                    // Determinar la serie según el tipo de comprobante
                                                    switch ($item->tipo_comprobante) {
                                                        case 'FACTURA':
                                                            $serie = 'F001';
                                                            break;
                                                        case 'BOLETA':
                                                            $serie = 'B002';
                                                            break;
                                                        case 'NOTA VENTA':
                                                            $serie = 'NT00';
                                                            break;
                                                        default:
                                                            $serie = '';
                                                    }
                                                @endphp
                                                <option value="{{ $item->id }}" data-impuesto="{{ $impuesto }}"
                                                    data-serie="{{ $serie }}"
                                                    {{ $index === count($comprobantes) - 1 ? 'selected' : '' }}>
                                                    {{ $item->tipo_comprobante }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('comprobante_id')
                                        <small class="text-danger">{{ '*' . $message }}</small>
                                    @enderror
                                </div>

                                <!--Numero de comprobante-->
                                <div class="col-5">
                                    <label for="numero_comprobante" class="form-label custom-label"
                                        style="color: black;">Numero:</label>
                                    <input readonly required type="text" name="numero_comprobante"
                                        id="numero_comprobante" class="form-control" value="{{ $numeroComprobante }}">
                                    @error('numero_comprobante')
                                        <small class="text-danger">{{ '*' . $message }}</small>
                                    @enderror
                                </div>
                                <!-- Serie -->
                                <div class="col-4">
                                    <label for="serie" class="form-label custom-label"
                                        style="color: black">Serie:</label>
                                    <input readonly type="text" name="serie" id="serie" value="F001"
                                        class="form-control border-success">
                                    @error('serie')
                                        <small class="text-danger">{{ '*' . $message }}</small>
                                    @enderror
                                </div>

                                <!-- Impuesto -->
                                <div class="col-3">
                                    <label for="igv_impuesto" class="form-label custom-label"
                                        style="color: black">(IGV):</label>
                                    <input readonly type="text" name="igv_impuesto" id="igv_impuesto" value="18%"
                                        class="form-control border-success">
                                    <input type="hidden" name="impuesto" id="impuesto" value="0">
                                    @error('impuesto')
                                        <small class="text-danger">{{ '*' . $message }}</small>
                                    @enderror
                                </div>

                                <!--Fecha Emision--->
                                <div class="col-6">
                                    <label for="fecha_hora" class="form-label custom-label" style="color: black;">F.
                                        Emisión:</label>
                                    @can('modificar-fecha-venta')
                                        <input type="datetime-local" name="fecha_hora" id="fecha_hora"
                                            class="form-control border-success"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                            min="{{ \Carbon\Carbon::now()->subDays(3)->format('Y-m-d\TH:i') }}"
                                            max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                    @else
                                        <input readonly type="datetime-local" name="fecha_hora" id="fecha_hora"
                                            class="form-control border-success"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                            min="{{ \Carbon\Carbon::now()->subDays(3)->format('Y-m-d\TH:i') }}"
                                            max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                    @endcan
                                </div>
                                @can('modificar-fecha-venta')
                                    <!-- FechaVencimiento -->
                                    <div class="col-6">
                                        <label for="fecha_vencimiento" class="form-label custom-label"
                                            style="color: black;">F.
                                            Vencimiento:</label>
                                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                            class="form-control border-success"
                                            value="{{ \Carbon\Carbon::now()->addDays(2)->format('Y-m-d') }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            max="{{ \Carbon\Carbon::now()->addDays(2)->format('Y-m-d') }}">
                                    </div>
                                @else
                                    <!-- FechaVencimiento -->
                                    <div class="col-sm-6">
                                        <label for="fecha_vencimiento" class="form-label" style="color: black;">F.
                                            Vencimiento:</label>
                                        <input readonly type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                            class="form-control border-success"
                                            value="{{ \Carbon\Carbon::now()->addDays(2)->format('Y-m-d') }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            max="{{ \Carbon\Carbon::now()->addDays(2)->format('Y-m-d') }}">
                                    </div>
                                @endcan

                                <!--Cliente-->
                                <div class="col-10">
                                    <label for="cliente_id" class="form-label custom-label"
                                        style="color: black;">Cliente:</label>
                                    <select required name="cliente_id" id="cliente_id" class="form-control select2"
                                        title="Selecciona">
                                        @foreach ($clientes as $item)
                                            <option value="{{ $item->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                {{ $item->persona->numero_documento . '-' . $item->persona->razon_social }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cliente_id')
                                        <small class="text-danger">{{ '*' . $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-1">
                                    <!-- <small><a href="#">(+ Añadir Nuevo Cliente)</a></small> -->
                                    <label for="nuevocliente" class="form-label custom-label" style="color: black;">
                                        Ag:</label>
                                    <button type="button" class="btn icon btn-success" data-bs-toggle="modal"
                                        data-bs-target="#inlineForm">
                                        <a href="#"><i data-feather="plus" style="color: black;"></i></a>
                                    </button>
                                </div>

                                <!----User--->
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                                <!--Botones--->
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-success icon" id="guardar">REALIZAR VENTA
                                        <i class="fa-solid fa-cart-arrow-down"></i></button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!------venta producto---->
                    <div class="col-xl-8">
                        <div class="text-white bg-dark p-1 text-center mb-1" style="border-radius: 15px;">
                            Detalles de la venta
                        </div>
                        <div class="p-3 border border-info" style="border-radius: 15px;">
                            <div class="row gy-4">

                                <!-----Producto---->
                                <div class="col-12">
                                    <select name="producto_id" id="producto_id" class="form-control select2"
                                        title="Busque un producto aquí" style="color: white !important;">
                                        <option value="" selected disabled>Seleccione un producto</option>
                                        @foreach ($productos as $item)
                                            <option
                                                value="{{ $item->id }}-{{ $item->stock }}-{{ $item->precio_venta }}">
                                                {{ $item->nombre . ' - ' . $item->codigo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Stock -->
                                <div class="row mb-2 mt-2 justify-content-end">
                                    <div class="col-12 col-sm-6">
                                        <div class="row align-items-center">
                                            <label for="stock"
                                                class="col-form-label col-4 text-dark">Stock:</label>
                                            <div class="col-8">
                                                <input disabled id="stock" type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Precio de Venta, Cantidad y Descuento -->
                                <div class="row">
                                    <!-- Precio de Venta -->
                                    <div class="col-sm-4">
                                        <label for="precio_venta" class="form-label text-dark custom-label">Precio de
                                            venta:</label>
                                        <input disabled type="number" name="precio_venta" id="precio_venta"
                                            class="form-control" step="0.1">
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="col-sm-4">
                                        <label for="cantidad"
                                            class="form-label text-dark custom-label">Cantidad:</label>
                                        <input type="number" name="cantidad" id="cantidad" class="form-control">
                                    </div>

                                    <!-- Descuento -->
                                    <div class="col-sm-4">
                                        <label for="descuento"
                                            class="form-label text-dark custom-label">Descuento:</label>
                                        <input type="number" name="descuento" id="descuento" class="form-control">
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="row mt-3 mb-1">
                                    <div class="col d-flex justify-content-between">
                                        <!-- Botón Libre -->
                                        <button id="btnAgregarProductoLibre" class="btn btn-success" type="button">
                                            Libre <i class="fa-regular fa-square-plus"></i>
                                        </button>

                                        <!-- Botón Agregar -->
                                        <button id="btn_agregar" class="btn btn-primary" type="button">
                                            Agregar <i class="fa-solid fa-plus-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-----Tabla para el detalle de la venta--->
                                <div class="col-12">
                                    <div class="table-responsive table-scrollable">
                                        <table id="tabla_detalle" class="table table-hover">
                                            <thead class="bg-black">
                                                <tr>
                                                    <th class="text-black col-1">#</th>
                                                    <th class="text-black col-4">Name.Producto</th>
                                                    <th class="text-black col-2">Cant.Product</th>
                                                    <th class="text-black col-2">Precio.Venta</th>
                                                    <th class="text-black col-2">Descuento.</th>
                                                    <th class="text-black col-2">Subtotal</th>
                                                    <th class="text-black col-1">Elim.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- <tr></tr> --}}
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th colspan="4">Sumas</th>
                                                    <th colspan="2"><span id="sumas">0</span></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th colspan="4">IGV %</th>
                                                    <th colspan="2"><span id="igv">0</span></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th colspan="4">Total</th>
                                                    <th colspan="2"> <input type="hidden" name="total"
                                                            value="0" id="inputTotal"> <span
                                                            id="total">0</span>
                                                    </th>
                                                </tr>
                                            </tfoot>

                                        </table>

                                    </div>
                                    {{-- METODO DE PAGO  --}}
                                    <div class="col-12 mt-1">
                                        <div class="row gy-2">
                                            <div class="col-4">
                                                <label for="metodo_pago">Método de Pago:</label>
                                                <select name="metodo_pagos" id="metodo_pago" class="form-select">
                                                    @foreach ($metodosPago as $metodoPago)
                                                        <option value="{{ $metodoPago->id }}"
                                                            data-tipo="{{ $metodoPago->nombre }}">
                                                            {{ $metodoPago->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div id="campo_monto_recibido" class="col-4" style="display: none;">
                                                <label for="monto_recibido">Monto Recibido:</label>
                                                <input type="number" step="0.1" name="monto_recibido"
                                                    id="monto_recibido" class="form-control" value="0">
                                            </div>

                                            <div id="campo_numero_operacion" class="col-6" style="display: none;">
                                                <label for="numero_operacion">Número de Operación:</label>
                                                <input type="text" name="numero_operacion" id="numero_operacion"
                                                    class="form-control">
                                            </div>

                                            <div id="vuelto_monto" class="col-4">
                                                <label for="vuelto">Vuelto:</label> <br>
                                                <span id="vuelto_span"><span
                                                        class="badge bg-success">0.00</span></span>
                                                <!-- <input readonly type="number" name="vuelto" id="vuelto_input" class="form-control"> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--Boton para cancelar venta--->
                                <div class="col-12">
                                    <button id="cancelar" type="button" class="btn btn-danger"
                                        data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Cancelar venta
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>

    <!-- Modal para cancelar la venta -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Advertencia</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres cancelar la venta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btnCancelarVenta" type="button" class="btn btn-danger"
                        data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Productos precargados
                const productosPrecargados = @json($productosPrecargados);

                if (productosPrecargados.length > 0) {
                    productosPrecargados.forEach(producto => {
                        // Construir el valor del producto (ID - Stock - Precio)
                        const productoData = `${producto.id}-${producto.stock}-${producto.precio_venta || 0}`;

                        // Seleccionar el producto en el select (suponiendo que tienes un select para elegir productos)
                        $('#producto_id').val(productoData).trigger('change');

                        // Establecer los valores iniciales en los campos de formulario
                        $('#cantidad').val(producto.cantidad || 1); // Si no hay cantidad, usar 1
                        $('#precio_venta').val(producto.precio_venta || 0); // Si no hay precio, usar 0
                        $('#descuento').val(producto.descuento || 0); // Si no hay descuento, usar 0
                        $('#stock').val(producto.stock); // Asignar stock de producto correctamente

                        // Agregar el producto a la tabla (dependiendo de tu lógica de agregar productos)
                        agregarProducto();
                    });

                    // Recalcular totales después de agregar los productos
                    recalcularTotales();
                    mostrarTotales();
                }
            });

            // Inicializar Select2 para el campo de producto
            $('#producto_id').select2({
                placeholder: "Selecciona un producto",
                allowClear: true,
                width: '100%'
            });
            // select2 de clientes
            $(document).ready(function() {
                // Inicializar Select2 para el campo de clientes
                $('#cliente_id').select2({
                    placeholder: "Selecciona un cliente",
                    allowClear: true,
                    width: '100%' // Asegura que Select2 ocupe el 100% del contenedor
                });
                $('#comprobante_id, #cliente_id').on('change', function() {
                    validarNumeroDocumento();
                });

            });

            //AQUI MANEJAMOS PARA DESGLOSAR EL METODO DE PAGO
            document.addEventListener('DOMContentLoaded', function() {
                const metodoPagoSelect = document.getElementById('metodo_pago');
                const campoMontoRecibido = document.getElementById('campo_monto_recibido');
                const campoNumeroAprobacion = document.getElementById('campo_numero_operacion');
                const campoVueltoMonto = document.getElementById('vuelto_monto');

                metodoPagoSelect.addEventListener('change', function() {
                    const selectedOption = metodoPagoSelect.options[metodoPagoSelect.selectedIndex];
                    const tipoMetodoPago = selectedOption.getAttribute('data-tipo');

                    if (tipoMetodoPago === 'Efectivo') {
                        campoMontoRecibido.style.display = 'block';
                        campoVueltoMonto.style.display = 'block';
                        campoNumeroAprobacion.style.display = 'none';
                        //al cambio vaciar el valor de numero de operacion
                        $('#numero_operacion').val('');
                    } else {
                        campoMontoRecibido.style.display = 'none';
                        campoVueltoMonto.style.display = 'none';
                        campoNumeroAprobacion.style.display = 'block';
                        //al cambio vaciar el valor del monto recibido
                        $('#monto_recibido').val('');
                    }
                });

                // Trigger change event initially to set initial state
                metodoPagoSelect.dispatchEvent(new Event('change'));
            });
            // Declaración de regex fuera de la función si se reutilizan
            const REGEX_FACTURA = /^(10|20|17|21|22|30)\d{9}$/;
            const REGEX_BOLETA = /^(?!([0-9])\1{7})\d{8}$/;

            // Función auxiliar para mostrar error
            function mostrarError(mensaje, tipo = 'error') {
                showModal(mensaje, tipo);
                reproducirSonidoAlerta();
            }

            // Función para validar el número de documento según el tipo de comprobante
            function validarNumeroDocumento() {
                // Obtener elementos
                const comprobanteSelect = document.getElementById('comprobante_id');
                const clienteSelect = document.getElementById('cliente_id');
                const tipoComprobante = comprobanteSelect?.options[comprobanteSelect.selectedIndex]?.text.toUpperCase();
                const clienteId = clienteSelect?.value || '';
                const clienteText = clienteSelect?.options[clienteSelect.selectedIndex]?.text || '';
                const numeroDocumento = clienteText.split('-')[0].trim();
                const total = parseFloat($('#total').text()) || 0;

                // Validaciones generales
                if (!tipoComprobante || !clienteSelect) {
                    mostrarError('Hubo un problema con los datos seleccionados.');
                    return false;
                }

                if (!clienteId) {
                    mostrarError('Debe seleccionar un cliente para continuar.');
                    return false;
                }

                // Validación específica por tipo de comprobante
                if (tipoComprobante === 'FACTURA') {
                    if (!REGEX_FACTURA.test(numeroDocumento)) {
                        mostrarError('Para una Factura, seleccione un cliente con RUC válido.');
                        return false;
                    }
                }

                if (tipoComprobante === 'BOLETA') {
                    if (total >= 700 && !REGEX_BOLETA.test(numeroDocumento)) {
                        mostrarError(
                            'Para una Boleta con un monto total de 700 o más, seleccione un cliente con DNI válido.'
                        );
                        return false;
                    }
                }

                if (tipoComprobante === 'NOTA VENTA') {
                    if (clienteId === '') {
                        mostrarError('Seleccione un Cliente para la Nota de Venta.');
                        return false;
                    }
                }

                // Si todo está bien
                return true;
            }

            function calcularVuelto() {
                const total = parseFloat($('#total').text()) || 0;
                const montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
                const vuelto = montoRecibido - total;

                // Actualizar el texto del vuelto
                $('#vuelto_span').text(vuelto.toFixed(2));

                // Opcional: Si necesitas actualizar un input oculto
                // $('#vuelto_input').val(vuelto.toFixed(2));

                // Remover todas las clases de estado anteriores
                $('#vuelto_span')
                    .removeClass('bg-success bg-info bg-danger bg-light');

                // Asignar la clase correspondiente según el valor del vuelto
                if ((montoRecibido === 0 || montoRecibido === '') && total === 0) {
                    // No hacer nada o establecer un estado predeterminado
                    $('#vuelto_span').addClass('badge bg-light');
                } else if (vuelto > 0) {
                    $('#vuelto_span').addClass('badge bg-info');
                } else if (vuelto < 0) {
                    $('#vuelto_span').addClass('badge bg-danger');
                } else {
                    $('#vuelto_span').addClass('badge bg-success');
                }
            }

            // Event listeners para actualizar el vuelto en tiempo real
            $(document).ready(function() {
                $('#monto_recibido').on('input', calcularVuelto);
                $('#total').on('input',
                    calcularVuelto); // Asegúrate de que el total también actualice el vuelto si cambia
            });

            // TODO DE VENTAS DESDE AQUI
            $(document).ready(function() {

                $('#producto_id').change(mostrarValores);


                $('#btn_agregar').click(function() {
                    agregarProducto();
                });

                // Evento para capturar cambios en cantidad, precio de venta y descuento (focusout->.on('input')
                let updatingRow = false;
                // Reemplaza 'input' por 'blur' o 'change'
                // Cambia el evento de 'input' para calcular y actualizar totales en tiempo real
                $('#tabla_detalle').on('input',
                    'input[name^="arraycantidad"], input[name^="arrayprecioventa"], input[name^="arraydescuento"]',
                    function() {
                        // Permitir que los campos se vacíen
                        let cantidadInput = $(this).closest('tr').find('input[name^="arraycantidad"]');
                        let precioVentaInput = $(this).closest('tr').find('input[name^="arrayprecioventa"]');
                        let descuentoInput = $(this).closest('tr').find('input[name^="arraydescuento"]');

                        // Validar y obtener los valores
                        let cantidad = parseFloat(cantidadInput.val()) || 0; // Permitir valor 0 si está vacío
                        let precioVenta = parseFloat(precioVentaInput.val()) || 0; // Permitir valor 0 si está vacío
                        let descuento = parseFloat(descuentoInput.val()) || 0; // Permitir valor 0 si está vacío

                        // Calcular subtotal
                        let subtotalProducto = round(cantidad * precioVenta - descuento);
                        $(this).closest('tr').find('td:nth-child(6)').text(subtotalProducto.toFixed(2));

                        // Actualizar totales
                        recalcularTotales();
                        mostrarTotales();
                        calcularVuelto();
                    });

                $('#btnCancelarVenta').click(function() {
                    cancelarVenta();
                });

                disableButtons();

                $('#impuesto').val(impuesto + '%');

                // Evento para el botón "Agregar Producto Libre"
                $('#btnAgregarProductoLibre').click(function() {
                    agregarProductoLibre();
                });

                $('#monto_recibido').on('input', function() {
                    calcularVuelto();
                });

                // **Manejador de Evento para el Envío del Formulario**
                $('#formVenta').on('submit', function(e) {
                    // Primero, recalcular totales para asegurarse de que estén actualizados
                    recalcularTotales();
                    mostrarTotales();
                    // Primero, realizar la validación del número de documento
                    if (!validarNumeroDocumento()) {
                        e.preventDefault(); // Evitar el envío del formulario
                        return; // Salir de la función
                    }

                    // Inicializar banderas de validación
                    let hasZeroSubtotal = false;
                    let hasNegativeValues = false;
                    let validationMessages = [];

                    // Verificar si la tabla de detalles tiene filas
                    if ($('#tabla_detalle tbody tr').length === 0) {
                        e.preventDefault(); // Evitar el envío del formulario
                        reproducirSonidoAlerta();
                        showModal(
                            'No hay productos agregados.',
                            'warning' // Puedes usar 'error' o cualquier otro tipo de modal que prefieras
                        );
                        return; // Salir de la función
                    }

                    // Recorrer cada fila de la tabla de detalles para validar los campos
                    $('#tabla_detalle tbody tr').each(function(index) {
                        // Obtener los valores de los campos
                        let cantidad = parseFloat($(this).find('input[name^="arraycantidad"]').val());
                        let precioVenta = parseFloat($(this).find('input[name^="arrayprecioventa"]')
                            .val());
                        let descuento = parseFloat($(this).find('input[name^="arraydescuento"]').val());
                        let subtotalText = $(this).find('td:nth-child(6)').text();
                        let subtotal = parseFloat(subtotalText.replace(',',
                            '.')); // Asegura el manejo de decimales

                        // Validar que cantidad sea un número válido y >= 1
                        if (isNaN(cantidad) || cantidad < 1) {
                            hasNegativeValues = true;
                            validationMessages.push(
                                `La cantidad en la fila ${index + 1} debe ser al menos 1.`);
                        }

                        // Validar que precioVenta sea un número válido y >= 0.01
                        if (isNaN(precioVenta) || precioVenta < 0.01) {
                            hasNegativeValues = true;
                            validationMessages.push(
                                `El precio de venta en la fila ${index + 1} debe ser al menos 0.01.`
                            );
                        }

                        // Validar que descuento sea un número válido y >= 0
                        if (isNaN(descuento) || descuento < 0) {
                            hasNegativeValues = true;
                            validationMessages.push(
                                `El descuento en la fila ${index + 1} no puede ser negativo.`);
                        }

                        // Validar que subtotal sea un número válido y >= 0
                        if (isNaN(subtotal) || subtotal < 0) {
                            hasNegativeValues = true;
                            validationMessages.push(
                                `El subtotal en la fila ${index + 1} no puede ser negativo.`);
                        }

                        // Verificar si el subtotal es <= 0
                        if (isNaN(subtotal) || subtotal <= 0) {
                            hasZeroSubtotal = true;
                        }
                    });

                    // Si existen filas con subtotal <= 0
                    if (hasZeroSubtotal) {
                        e.preventDefault(); // Evitar el envío del formulario
                        reproducirSonidoAlerta();
                        showModal(
                            'Hay una o más filas con subtotal en 0 o menor. Por favor, corrígelas antes de continuar.',
                            'error'
                        );
                        // No es necesario continuar con otras validaciones
                        return;
                    }

                    // Si existen valores negativos o inválidos
                    if (hasNegativeValues) {
                        e.preventDefault(); // Evitar el envío del formulario
                        reproducirSonidoAlerta();
                        // Combinar todos los mensajes de error en uno solo
                        showModal(
                            validationMessages.join('<br>'),
                            'error'
                        );
                        return;
                    }

                    // Opcional: Validaciones adicionales que podrías considerar
                    // 1. Validar que la fecha de vencimiento sea posterior a la fecha de emisión
                    let fechaEmision = new Date($('#fecha_hora').val());
                    let fechaVencimiento = new Date($('#fecha_vencimiento').val());
                    if (fechaVencimiento <= fechaEmision) {
                        e.preventDefault();
                        reproducirSonidoAlerta();
                        showModal(
                            'La fecha de vencimiento debe ser posterior a la fecha de emisión.',
                            'error'
                        );
                        return;
                    }

                    // 2. Validar que el monto recibido sea suficiente si el método de pago es 'Efectivo'
                    let metodoPago = $('#metodo_pago option:selected').attr('data-tipo');
                    if (metodoPago === 'Efectivo') {
                        let montoRecibido = parseFloat($('#monto_recibido').val());
                        if (isNaN(montoRecibido) || montoRecibido < total) {
                            e.preventDefault();
                            reproducirSonidoAlerta();
                            showModal(
                                'El monto recibido debe ser igual o mayor al total de la venta.',
                                'error'
                            );
                            return;
                        }
                    }
                    // Proceder con el envío del formulario si todas las validaciones pasan
                });

            });

            //Variables
            let cont = 0;
            let subtotal = [];
            let sumas = 0;
            let igv = 0;
            let total = 0;

            //Constantes
            // const impuesto = 18;
            const impuesto = 18;

            function mostrarValores() {
                let dataProducto = document.getElementById('producto_id').value.split('-');
                // console.log(dataProducto); // Verifica la estructura del array

                // Si el array tiene más de 3 elementos, significa que el stock es negativo
                if (dataProducto.length > 3) {
                    // Unimos los elementos que corresponden al stock negativo
                    let stockNegativo = dataProducto[1] + '-' + dataProducto[2];
                    let precio = dataProducto[3] ? dataProducto[3] : 0;

                    $('#stock').val(stockNegativo);
                    $('#precio_venta').val(precio);
                } else {
                    // Si no hay stock negativo, asignamos los valores normalmente
                    let stock = dataProducto[1] ? dataProducto[1] : 0;
                    let precio = dataProducto[2] ? dataProducto[2] : 0;

                    $('#stock').val(stock);
                    $('#precio_venta').val(precio);
                }
            }

            // Función para reproducir el sonido de alerta
            function reproducirSonidoAlerta() {
                let audioElement = document.getElementById('audio-alert');
                if (audioElement) {
                    audioElement.play().then(() => {
                        console.log('Audio playing successfully.');
                    }).catch(error => {
                        console.error('Error playing audio:', error);
                    });
                } else {
                    console.error('No se pudo encontrar el elemento de audio con ID "audio-alert".');
                }
            }

            function agregarProducto() {
                // Obtener valores de los campos
                let productoSeleccionado = $('#producto_id').val();
                if (!productoSeleccionado) {
                    reproducirSonidoAlerta();
                    showModal('Debe seleccionar un producto.');
                    return;
                }

                // Obtener valores del producto
                let dataProducto = productoSeleccionado.split('-');
                let idProducto = dataProducto[0];
                let nameProducto = $('#producto_id option:selected').text();
                let cantidad = parseFloat($('#cantidad').val()) || 1; // Predeterminar a 1 si es NaN
                let precioVenta = parseFloat($('#precio_venta').val()) || 0; // Predeterminar a 0 si es NaN
                let descuento = parseFloat($('#descuento').val()) || 0; // Predeterminar a 0 si es NaN
                let stock = parseInt($('#stock').val()) || 0; // Predeterminar a 0 si es NaN

                // Validar cantidad
                if (cantidad <= 0) {
                    reproducirSonidoAlerta();
                    showModal('La cantidad ingresada debe ser mayor a 0.');
                    return;
                }

                // Validar descuento
                if (descuento < 0) {
                    reproducirSonidoAlerta();
                    showModal('El descuento ingresado no puede ser negativo.');
                    return;
                }

                // Validar stock disponible
                if (cantidad > stock) {
                    reproducirSonidoAlerta();
                    showModalWarning('La cantidad ingresada supera el stock disponible.');
                    // Puedes permitir continuar o retornar dependiendo de tu lógica:
                    // return;
                }

                // Verificar si el producto ya está en la tabla
                let indiceExistente = -1;
                $('#tabla_detalle tbody tr').each(function(index) {
                    let idProductoExistente = $(this).find('input[name^="arrayidproducto"]').val();
                    if (idProductoExistente == idProducto) {
                        indiceExistente = index;
                        return false; // Salir del bucle
                    }
                });

                if (indiceExistente >= 0) {
                    // Actualizar la cantidad si el producto ya está en la tabla
                    let cantidadExistente = parseFloat(
                        $('#tabla_detalle tbody tr').eq(indiceExistente).find('input[name^="arraycantidad"]').val()
                    ) || 0;

                    cantidadExistente += cantidad;
                    $('#tabla_detalle tbody tr').eq(indiceExistente).find('input[name^="arraycantidad"]').val(
                        cantidadExistente);

                    // Recalcular subtotal del producto existente
                    actualizarFila(indiceExistente);
                    reproducirSonidoAlerta();
                    showModalInfo('Se actualizó la cantidad del producto.');
                } else {
                    // Calcular el subtotal para un nuevo producto
                    let subtotalProducto = round(cantidad * precioVenta - descuento);

                    let fila = '<tr id="fila' + cont + '">' +
                        '<th class="col-1">' + (cont + 1) + '</th>' +
                        '<td class="col-6 producto-nombre"><input type="hidden" name="arrayidproducto[' + cont + ']" value="' +
                        idProducto + '">' + nameProducto + '</td>' +
                        '<td class="col-1"><input type="number" step="1" min="1" class="form-control" name="arraycantidad[' +
                        cont +
                        ']" value="' + cantidad + '" onchange="actualizarFila(' + cont + ')"></td>' +
                        '<td class="col-1"><input type="hidden" min="0.01" step="0.01" name="arrayprecioventa[' + cont +
                        ']" value="' + precioVenta +
                        '">' + precioVenta + '</td>' +
                        '<td class="col-1"><input type="number" step="0.1" min="0" class="form-control" name="arraydescuento[' +
                        cont +
                        ']" value="' + descuento + '" onchange="actualizarFila(' + cont + ')"></td>' +
                        '<td class="col-2">' + subtotalProducto + '</td>' +
                        '<td class="col-1"><button style="padding: 0; border: none; background: none;" type="button" onClick="eliminarProducto(' +
                        cont + ')"><i class="fa-solid fa-circle-xmark" style="color: #b80000;"></i></button></td>' +
                        '</tr>';

                    // Añadir la fila a la tabla
                    $('#tabla_detalle').append(fila);

                    // Incrementar el contador
                    cont++;
                }

                // Recalcular totales y limpiar campos
                recalcularTotales();
                limpiarCampos();
                disableButtons();

                // Mostrar totales actualizados
                mostrarTotales();
                calcularVuelto();
            }

            // Función para actualizar la fila (puedes usar esta función como antes)
            function actualizarFila(indice) {
                let cantidadInput = $('input[name="arraycantidad[' + indice + ']"]');
                let precioVentaInput = $('input[name="arrayprecioventa[' + indice + ']"]');
                let descuentoInput = $('input[name="arraydescuento[' + indice + ']"]');
                let nombreProductoInput = $('input[name="arraynombre[' + indice + ']"]');

                let cantidad = parseFloat(cantidadInput.val()) || 0; // Permitir valor 0 si está vacío
                let precioVenta = parseFloat(precioVentaInput.val()) || 0; // Permitir valor 0 si está vacío
                let descuento = parseFloat(descuentoInput.val()) || 0; // Permitir valor 0 si está vacío

                // Calcular el subtotal
                let subtotalProducto = round(cantidad * precioVenta - descuento);
                $('#tabla_detalle tbody tr').eq(indice).find('td:nth-child(6)').text(subtotalProducto.toFixed(2));
                // Obtener y validar el nombre del producto si existe
                let nombreProducto = '';
                if (nombreProductoInput.length) {
                    nombreProducto = nombreProductoInput.val().trim();
                    if (nombreProducto === '') {
                        reproducirSonidoAlerta();
                        showModal('El nombre del producto no puede estar vacío.', 'error');
                        // Recalcular totales y mostrarlos
                        recalcularTotales();
                        mostrarTotales();
                        calcularVuelto();
                        return;
                    }
                }
                // Recalcular totales y mostrarlos
                recalcularTotales();
                mostrarTotales();
                calcularVuelto();
            }

            function recalcularTotales() {
                // Inicializar el arreglo subtotal
                subtotal = [];

                // Obtener el valor dinámico del IGV desde el campo #igv_impuesto
                let impuestoText = $('#igv_impuesto').val(); // Ejemplo: "18%" o "0%"
                let impuesto = parseFloat(impuestoText.replace('%', '')); // Convertir a número

                // Iterar sobre cada fila de la tabla de detalles
                $('#tabla_detalle tbody tr').each(function(index) {
                    let cantidad = parseFloat($(this).find('input[name^="arraycantidad"]').val());
                    let precioVenta = parseFloat($(this).find('input[name^="arrayprecioventa"]').val());
                    let descuento = parseFloat($(this).find('input[name^="arraydescuento"]').val());

                    // Validar y asignar valores predeterminados si es necesario
                    if (isNaN(cantidad) || cantidad < 0) {
                        cantidad = 1; // Cantidad predeterminada
                    }
                    if (isNaN(descuento) || descuento < 0) {
                        descuento = 0; // Descuento predeterminado
                    }
                    if (isNaN(precioVenta)) {
                        precioVenta = 0; // Precio de venta predeterminado
                    }

                    // Calcular el subtotal del producto
                    let subtotalProducto = round(cantidad * precioVenta - descuento);
                    subtotal[index] = subtotalProducto;

                    // Actualizar el subtotal mostrado en la tabla
                    $(this).find('td:nth-child(6)').text(subtotalProducto.toFixed(2));
                });

                // Calcular los totales generales
                sumas = subtotal.reduce((acc, curr) => acc + round(curr), 0);
                igv = round(sumas / 100 * impuesto);
                total = round(sumas + igv);
            }

            function mostrarTotales() {
                // Redondear y mostrar sumas con solo dos decimales
                $('#sumas').html(sumas.toFixed(2)); // Esto mostrará el valor de sumas con dos decimales
                $('#igv').html(igv);
                $('#total').html(total);
                $('#impuesto').val(igv);
                $('#inputTotal').val(total);

                disableButtons();
            }

            function agregarProductoLibre() {
                let cantidad = 1;
                let descuento = 0;
                let precioVenta = 0; // Asignar un valor por defecto para evitar errores

                let subtotalProducto = round(cantidad * precioVenta - descuento);
                subtotal[cont] = subtotalProducto;

                let fila = '<tr id="filaLibre' + cont + '">' +
                    '<th class="col-1">' + (cont + 1) + '</th>' +
                    '<td class="col-6 producto-nombre"><input type="text" class="form-control" name="arraynombre[' + cont +
                    ']" placeholder="Nombre del producto" onchange="actualizarFila(' + cont + ')"></td>' +
                    '<td class="col-1"><input type="number" step="1" min="1" class="form-control" name="arraycantidad[' + cont +
                    ']" value="' + cantidad + '" placeholder="Cantidad" onchange="actualizarFila(' + cont + ')"></td>' +
                    '<td class="col-1"><input type="number" min="0.01" step="0.01" class="form-control" name="arrayprecioventa[' +
                    cont +
                    ']" placeholder="Precio de venta" onchange="actualizarFila(' + cont + ')"></td>' +
                    '<td class="col-1"><input type="number" step="0.1" min="0" class="form-control" name="arraydescuento[' +
                    cont +
                    ']" value="' + descuento + '" placeholder="Descuento" onchange="actualizarFila(' + cont + ')"></td>' +
                    '<td class="col-2">' + subtotalProducto + '</td>' +
                    '<td class="col-1"><button style="padding: 0; border: none; background: none;" type="button" onClick="eliminarProductoLibre(' +
                    cont + ')"><i class="fa-solid fa-circle-xmark" style="color: #b80000;"></i></button></td>' +
                    '</tr>';

                // Añadir la fila a la tabla
                $('#tabla_detalle').append(fila);

                // Incrementar contador de productos
                cont++;

                // Recalcular totales y limpiar campos
                recalcularTotales();
                disableButtons();

                // Mostrar los totales actualizados
                mostrarTotales();
                //calcular vuelto
                calcularVuelto();
            }

            function eliminarProductoLibre(indice) {
                // Eliminar la fila de la tabla
                $('#filaLibre' + indice).remove();

                // Eliminar el producto del arreglo subtotal
                subtotal.splice(indice, 1);

                // Reorganizar índices de filas después de eliminar
                $('#tabla_detalle tbody tr').each(function(index) {
                    $(this).find('th:first').text(index + 1); // Actualizar número de fila
                    $(this).attr('id', 'filaLibre' + index); // Actualizar ID de la fila

                    // Actualizar los nombres e identificadores de los inputs y botones
                    $(this).find('input').each(function() {
                        let name = $(this).attr('name');
                        let newName = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', newName);
                    });
                    $(this).find('button').attr('onClick', 'eliminarProductoLibre(' + index + ')');
                    $(this).find('input[name^="arraycantidad"]').attr('onchange', 'actualizarFila(' + index + ')');
                    $(this).find('input[name^="arraydescuento"]').attr('onchange', 'actualizarFila(' + index + ')');
                });

                // Actualizar el contador de productos
                cont = $('#tabla_detalle tbody tr').length;

                actualizarFila(indice);
                // Recalcular totales y mostrar
                recalcularTotales();
                mostrarTotales();
                disableButtons();
                //calcular vuelto
                calcularVuelto();
            }

            function eliminarProducto(indice) {
                // Eliminar el producto del arreglo subtotal
                subtotal.splice(indice, 1);

                // Eliminar la fila de la tabla
                $('#fila' + indice).remove();

                // Reorganizar índices de filas después de eliminar
                $('#tabla_detalle tbody tr').each(function(index) {
                    $(this).find('th:first').text(index + 1); // Actualizar número de fila
                    $(this).attr('id', 'fila' + index); // Actualizar ID de la fila

                    // Actualizar los nombres e identificadores de los inputs y botones
                    $(this).find('input').each(function() {
                        let name = $(this).attr('name');
                        let newName = name.replace(/\[\d+\]/, '[' + index + ']');
                        $(this).attr('name', newName);
                    });
                    $(this).find('button').attr('onClick', 'eliminarProducto(' + index + ')');
                    $(this).find('input[name^="arraycantidad"]').attr('onchange', 'actualizarFila(' + index + ')');
                    $(this).find('input[name^="arraydescuento"]').attr('onchange', 'actualizarFila(' + index + ')');
                });

                // Actualizar el contador de productos
                cont = $('#tabla_detalle tbody tr').length;

                actualizarFila(indice);
                // Recalcular totales y mostrar
                recalcularTotales();
                mostrarTotales();
                disableButtons();
                //calcular vuelto
                calcularVuelto();
            }

            function cancelarVenta() {
                // Eliminar el tbody de la tabla
                $('#tabla_detalle tbody').empty();
                //volver a 0 el monto recibido
                $('#monto_recibido').val('');
                // Reiniciar valores de las variables
                cont = 0;
                subtotal = [];
                sumas = 0;
                igv = 0;
                total = 0;

                // Mostrar los campos calculados
                mostrarTotales();
                limpiarCampos();
                disableButtons();
                //calcular vuelto
                calcularVuelto();
            }

            function limpiarCampos() {
                let select = $('#producto_id');
                select.val(null).trigger('change'); // Establece el valor del select a la opción por defecto
                $('#cantidad').val('');
                $('#precio_venta').val('');
                $('#descuento').val('');
                $('#stock').val('');
            }

            function disableButtons() {
                let hasEmptyProductName = false;

                // $('#tabla_detalle tbody tr').each(function() {
                //     let productName = $(this).find('input[name^="arraynombre"]').val();
                //     if (typeof productName !== 'undefined' && productName.trim() === '') {
                //         hasEmptyProductName = true;
                //         return false;
                //     }
                // });

                if (total <= 0 || hasEmptyProductName) {
                    // $('#guardar').hide();
                    $('#cancelar').hide();
                } else {
                    // $('#guardar').show();
                    $('#cancelar').show();
                }
            }

            function showModal(message, icon = 'error') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: icon,
                    title: message
                })
            }

            function showModalInfo(message, icon = 'info') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: icon,
                    title: message
                })
            }

            function showModalWarning(message, icon = 'warning') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: icon,
                    title: message
                })
            }

            function round(num, decimales = 2) {
                var signo = (num >= 0 ? 1 : -1);
                num = num * signo;
                if (decimales === 0) //con 0 decimales
                    return signo * Math.round(num);
                // round(x * 10 ^ decimales)
                num = num.toString().split('e');
                num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
                // x * 10 ^ (-decimales)
                num = num.toString().split('e');
                return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
            }
        </script>
    @endpush
</x-app-layout>
