<x-app-layout>

    @section('title', 'Pedidos')
    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

    <audio id="audio-success">
        <source src="{{ asset('sound/success.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <div class="main-content container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Pedidos Online <i class="fa-solid fa-earth-americas fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" style="color: #0c8af1;">Inicio</a></li>
            <li class="breadcrumb-item active">Pedidos</li>
        </ol>

        <div class="col-md-12">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form action="{{ route('orden.index') }}" method="GET" class="row g-3">
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
        {{-- <div class="col-md-3">
        </div> --}}

        <div class="card">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Registro de Pedidos
            </div>

            <div class="card-body px-1 pb-1 table-responsive">
                <table id="orders-table" class="table table-hover table-striped table-bordered mb-0 text-dark">
                    <thead>
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Codigo</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                {{-- <td>{{ $order->id }}</td> --}}
                                <td>{{ $order->codigo_orden }}</td>
                                <td>{{ $order->user_web->name }}</td>
                                <td>
                                    @if ($order->estado == 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif ($order->estado == 'procesando')
                                        <span class="badge bg-info">Procesando</span>
                                    @elseif ($order->estado == 'completado')
                                        <span class="badge bg-success">Completado</span>
                                    @elseif ($order->estado == 'online')
                                        <span class="badge bg-dark"><i class="fa-solid fa-money-check-dollar"></i> |
                                            Online</span>
                                    @else
                                        <span class="badge bg-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light">s/. {{ number_format($order->total, 2) }}</span></td>
                                <td>
                                    <div>
                                        <p class="fw-semibold mb-1">
                                            <span class="m-1"><i class="fa-solid fa-calendar-days"></i></span>
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}
                                        </p>
                                        <p class="fw-semibold mb-0">
                                            <span class="m-1"><i class="fa-solid fa-clock"></i></span>
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('H:i:s') }}
                                        </p>
                                    </div>
                                </td>
                                {{-- botones para manejar los estados  --}}
                                <td>
                                    <button type="button" class="btn icon btn-light me-1"
                                        onclick="mostrarDetalles({{ $order->id }})">
                                        <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                    </button>

                                    @if ($order->estado == 'pendiente')
                                        <button type="button" class="btn icon btn-info"
                                            onclick="cambiarEstado({{ $order->id }}, 'procesando')">
                                            <i class="fa-solid fa-spinner"></i>
                                        </button>
                                        <button type="button" class="btn icon btn-danger ms-1"
                                            onclick="cambiarEstado({{ $order->id }}, 'cancelado')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @elseif ($order->estado == 'procesando')
                                        <button type="button" class="btn icon btn-dark"
                                            onclick="procesarOrden('{{ $order->codigo_orden }}')">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </button>
                                        <button type="button" class="btn icon btn-danger ms-1"
                                            onclick="cambiarEstado({{ $order->id }}, 'cancelado')">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @elseif ($order->estado == 'completado' && $order->estadoventa == 1 && $order->identificador_web_venta)
                                        <button type="button"
                                            class="btn icon btn-outline-dark me-2 mostrar-detalles-venta"
                                            data-id="{{ $order->id }}"
                                            data-identificador="{{ $order->identificador_web_venta }}">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                    @elseif ($order->estado == 'online' && $order->estadoventa == 0)
                                        <button type="button" class="btn icon btn-dark"
                                            onclick="procesarOrden({{ $order->id }})">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </button>
                                    @elseif ($order->estado == 'online' && $order->estadoventa == 1)
                                        <i class="fa-solid fa-circle-check" style="color: #03b300;"></i>
                                    @elseif ($order->estado == 'cancelado')
                                        <button type="button" class="btn icon btn-info"
                                            onclick="cambiarEstado({{ $order->id }}, 'procesando')">
                                            <i class="fa-solid fa-spinner"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- Modal vista previa -->
    <div id="dynamicModal" class="custom-modal" style="display: none;">
        <div class="modal-content">
            <span id="closeModal" class="close-button">&times;</span>
            <h1 id="modalTitle" class="font-semibold text-dark">Detalles del Pedido</h1>
            <hr>
            <div id="modalBody" class="text-dark">
                <!-- Aquí se llenarán los detalles dinámicamente -->
            </div>
        </div>
    </div>

    <style>
        /* Estilo para el botón de cerrar */
        .close-button {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            color: #fff;
            background-color: #000000;
            /* Fondo rojo suave */
            border: none;
            cursor: pointer;
            z-index: 1051;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            /* Hace que sea un círculo */
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .close-button:hover {
            background-color: #1f1f1f;
            /* Cambia a un rojo más intenso al pasar el mouse */
            transform: scale(1.2);
            /* Aumenta ligeramente el tamaño */
        }

        .close-button:focus {
            outline: none;
            /* Quita el borde azul al enfocarse */
        }

        .custom-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1050;
            /* Asegura que esté por encima del contenido */
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            position: relative;
        }
    </style>
    {{-- estilos de la tabla de registros  --}}
    <style>
        /* Estilo para los botones de paginación */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px 10px;
            margin: 0 2px;
            background-color: #f8f9fa;
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            transition: all 0.3s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #007bff;
            color: white;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            background-color: #0056b3;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background-color: #e9ecef;
            color: #6c757d;
            border: 1px solid #ddd;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            color: white;

            /* Espacio superior para la paginación */
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 20px;
            /* Ajusta el valor según tus necesidades */
        }
    </style>

    <style>
        /* Contenedor que envuelve las secciones de WhatsApp y Telegram */
        #whatsAppSection,
        #telegramSection {
            display: none;
            /* Oculto inicialmente */
        }
    </style>
    @push('js')
        <!-- Cargar jQuery y DataTables -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                const table = $('#orders-table').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
                    },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    destroy: true,
                    order: [
                        [4, 'desc']
                    ], // Columna de fecha (4) en orden descendente
                    columnDefs: [{
                            targets: 4,
                            type: 'date'
                        } // Asegúrate de que sea reconocida como tipo fecha
                    ],
                    dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-12"p>>',
                    pagingType: 'full_numbers',
                });

                // Función para actualizar los pedidos
                function actualizarPedidos() {
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;

                    $.ajax({
                        url: '/ordenes',
                        method: 'GET',
                        data: {
                            start_date: startDate,
                            end_date: endDate
                        },
                        success: function(response) {
                            // console.log(response);
                            table.clear();

                            response.forEach(order => {
                                const userName = order.user_web && order.user_web.name ? order
                                    .user_web.name :
                                    'Sin nombre';
                                const statusBadge = getOrderStatusBadge(order.estado);

                                const totalFormatted = isNaN(order.total) ? '0.00' : parseFloat(
                                    order.total).toFixed(2);
                                const totalBadge =
                                    `<span class="badge bg-light">s/. ${totalFormatted}</span>`; // Mantén el span y el color

                                const formattedDate =
                                    `<span class="m-1"><i class="fa-solid fa-calendar-days"></i></span> ${new Date(order.created_at).toLocaleDateString('es-ES')} 
                                       <span class="m-1"><i class="fa-solid fa-clock"></i></span> ${new Date(order.created_at).toLocaleTimeString('es-ES')}`;

                                const buttons = getActionButtons(order);

                                // Agregar fila
                                table.row.add([
                                    // order.id,
                                    order.codigo_orden,
                                    userName,
                                    statusBadge,
                                    totalBadge, // Usamos totalBadge en lugar de solo el valor
                                    formattedDate, // Usamos formattedDate con iconos
                                    buttons
                                ]);
                            });

                            table.draw();

                            // Asegurar que los iconos de Font Awesome se inicialicen
                            if (typeof FontAwesome !== 'undefined') {
                                FontAwesome.dom
                                    .i2svg(); // Esto asegura que los iconos sean correctamente renderizados
                            }
                        },
                        error: function() {
                            console.error('Error al obtener los pedidos.');
                        }
                    });
                }

                setInterval(actualizarPedidos, 10000);

                // Función para obtener el badge de estado
                function getOrderStatusBadge(estado) {
                    switch (estado) {
                        case 'pendiente':
                            return '<span class="badge bg-warning">Pendiente</span>';
                        case 'procesando':
                            return '<span class="badge bg-info">Procesando</span>';
                        case 'completado':
                            return '<span class="badge bg-success">Completado</span>';
                        case 'online':
                            return '<span class="badge bg-dark"><i class="fa-solid fa-money-check-dollar"></i> | Online</span>';
                        default:
                            return '<span class="badge bg-danger">Cancelado</span>';
                    }
                }

                // Función para obtener los botones de acción
                function getActionButtons(order) {
                    let buttons = '<div class="d-flex justify-content-start">';

                    buttons += `<button type="button" class="btn icon btn-light me-2 mostrar-detalles" data-id="${order.id}">
                                    <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                </button>`;

                    if (order.estado === 'pendiente') {
                        buttons +=
                            `<button type="button" class="btn icon btn-info me-2" onclick="cambiarEstado(${order.id}, 'procesando')"><i class="fa-solid fa-spinner"></i></button>`;
                        buttons +=
                            `<button type="button" class="btn icon btn-danger" onclick="cambiarEstado(${order.id}, 'cancelado')"><i class="fa-solid fa-xmark"></i></button>`;
                    } else if (order.estado === 'procesando') {
                        // Codificar el código de orden usando encodeURIComponent
                        const CodigoOrden = order.codigo_orden;
                        // Pasar el valor codificado en el onclick
                        buttons +=
                            `<button type="button" class="btn icon btn-dark me-2" onclick="procesarOrden('${CodigoOrden}')"><i class="fa-solid fa-cart-plus"></i></button>`;
                        buttons +=
                            `<button type="button" class="btn icon btn-danger" onclick="cambiarEstado(${order.id}, 'cancelado')"><i class="fa-solid fa-xmark"></i></button>`;
                    } else if (order.estado === 'completado' && order.estadoventa === 1 && order
                        .identificador_web_venta) {
                        buttons += `
                            <button type="button" class="btn icon btn-outline-dark me-2 mostrar-detalles-venta" data-id="${order.id}" data-identificador="${order.identificador_web_venta}">
                                <i class="fa-solid fa-print"></i>
                            </button>`;
                    } else if (order.estado === 'cancelado') {
                        buttons +=
                            `<button type="button" class="btn icon btn-info me-2" onclick="cambiarEstado(${order.id}, 'procesando')"><i class="fa-solid fa-xmark"></i></button>`;
                    }

                    buttons += '</div>';

                    return buttons;
                }

                // Mostrar detalles de las ordenes 
                $(document).on('click', '.mostrar-detalles', function() {
                    const orderId = $(this).data('id'); // Obtén el ID del atributo `data-id`
                    mostrarDetalles(orderId); // Llama a la función con el ID correspondiente
                });
                // rellenar el modal 
                window.mostrarDetalles = function(orderId) {
                    // Mostrar el spinner antes de hacer la solicitud
                    showSpinner();
                    $.ajax({
                        url: `/ordenes/${orderId}`,
                        method: 'GET',
                        success: function(data) {
                            const modalBody = document.getElementById('modalBody');
                            modalBody.innerHTML = `
                                <h4>🛍️ Estos son los detalles de su pedido:</h4>
                                <p><strong>🆔 COD:</strong> ${data.codigo_orden}</p>
                                <p><strong>👤 Nombre:</strong> ${data.nombre || 'Sin nombre'}</p>
                                <p><strong>💰 Total:</strong> s/. ${parseFloat(data.total).toFixed(2)}</p>
                                <p><strong>📱 Telf:</strong> 
                                    <a href="https://wa.me/${getCorrectPhoneNumber(data.telefono)}" target="_blank">
                                        ${getCorrectPhoneNumber(data.telefono)}
                                    </a>
                                </p>
                                <p><strong>🏠 Direcc:</strong> ${data.direccion}</p>
                                <p><strong>📝 Estado:</strong> ${data.estado}</p>
                                <p><strong>🗓️ Fecha:</strong> ${new Date(data.created_at).toLocaleString('es-ES')}</p>
                                <p><strong>🛒 Productos:</strong></p>
                                <ul>
                                    ${data.productos.map(producto => `
                                        <li>📦 ${producto.nombre} - Cantidad: ${producto.pivot.cantidad || 0} - Precio: s/. ${parseFloat(producto.pivot.precio || 0).toFixed(2)}</li>
                                    `).join('')}
                                </ul>`;

                            // Crear el mensaje de WhatsApp con emojis alineados sin espacio extra
                            let mensaje = `
                            *🛍️ Detalles de su pedido:*
                            *🆔 COD:* ${data.codigo_orden}
                            *👤 Nombre:* ${data.nombre || 'Sin nombre'}
                            *📱 Telf:* ${getCorrectPhoneNumber(data.telefono)}
                            *🏠 Direcc:* ${data.direccion}
                            *📝 Estado:* ${data.estado}
                            *🗓️ Fecha:* ${new Date(data.created_at).toLocaleString('es-ES')}
                            *🛒 Productos:*
                            ${data.productos.map(producto => `
                                📦 ${producto.nombre} - Cantidad: ${producto.pivot.cantidad || 0} - Precio: s/. ${parseFloat(producto.pivot.precio || 0).toFixed(2)}
                            `).join('\n')}
                            *💰 Total:* s/. ${parseFloat(data.total).toFixed(2)}
                            `;

                            // Codificar el mensaje para la URL, asegurándose de que los emojis y caracteres especiales se manejen correctamente
                            mensaje = encodeURIComponent(mensaje);

                            // Enlace para WhatsApp con el mensaje formateado
                            const whatsappUrl = `https://wa.me/${getCorrectPhoneNumber(data.telefono)}?text=${mensaje}`;

                            // Crear el enlace para enviar por WhatsApp
                            const whatsappLink = document.createElement('a');
                            whatsappLink.href = whatsappUrl;
                            whatsappLink.target = '_blank';
                            whatsappLink.innerHTML = `
                                <button class="btn btn-success" style="display: flex; align-items: center;">
                                    <i class="fa fa-whatsapp" style="margin-right: 8px;"></i> Enviar mensaje
                                </button>
                            `;
                            modalBody.appendChild(whatsappLink);

                            // Cambiar el título del modal a "Detalles de la Orden"
                            document.getElementById('modalTitle').innerText = "Detalles de la Orden";
                            abrirModal();
                        },
                        error: function() {
                            console.error('Error al obtener los detalles del pedido.');
                        },
                        complete: function() {
                            // Ocultar el spinner cuando la solicitud finalice
                            hideSpinner();
                        }
                    });
                };

                // Función para corregir el número de teléfono
                function getCorrectPhoneNumber(phone) {
                    // Si el número empieza con +51, lo dejamos como está
                    if (phone.startsWith('+51')) {
                        return phone;
                    }
                    // Si el número empieza con 51, lo convertimos a +51
                    if (phone.startsWith('51')) {
                        return '+' + phone;
                    }
                    // Si el número no empieza con 51, lo convertimos a +51
                    return '+51' + phone;
                }

                // mostrar detalles de las ordenes vendidas
                $(document).on('click', '.mostrar-detalles-venta', function() {
                    const orderId = $(this).data('id'); // Obtiene el ID de la orden
                    const identificadorVenta = $(this).data(
                        'identificador'); // Obtiene el identificador de la venta
                    mostrarDetallesVenta(orderId,
                        identificadorVenta); // Llama a la función para obtener los detalles de la venta
                });
                //rellenar el modal con los detalles de las ordenes vendidas
                function mostrarDetallesVenta(orderId, identificadorVenta) {
                    // Mostrar el spinner antes de hacer la solicitud
                    showSpinner();
                    $.ajax({
                        url: `/ordenes/${orderId}/venta/${identificadorVenta}`, // Ruta para obtener la venta por identificador
                        method: 'GET',
                        success: function(data) {
                            if (data.error) {
                                Swal.fire('Error', data.error, 'error');
                                return;
                            }

                            const modalBody = document.getElementById('modalBody');
                            modalBody.innerHTML = `
                                <p><strong>Identificador de Venta:</strong> ${data.identificador_web}</p>
                                <p><strong>Monto Total:</strong> s/. ${parseFloat(data.total).toFixed(2)}</p>
                                <p><strong>Fecha de Venta:</strong> ${new Date(data.fecha_hora).toLocaleString('es-ES')}</p>
                               
                                <div class="d-flex gap-3">
                                    <!-- Botón para imprimir en formato Ticket -->
                                    <button type="button" class="btn btn-primary icon" title="Imprimir en formato Ticket"
                                        onclick="abrirVentanaPDFTicket('/venta/${data.identificador_web}/ticket')">
                                        <i class="fa-solid fa-receipt"></i> Ticket
                                    </button>

                                    <!-- Botón para imprimir en formato A4 -->
                                    <button type="button" class="btn btn-secondary icon" title="Imprimir en formato A4"
                                        onclick="abrirVentanaPDFA4('/venta/${data.identificador_web}/ticket-a4')">
                                        <i class="fa-solid fa-file-pdf"></i> A4
                                    </button>

                                    <!-- Botones para enviar a WhatsApp y Telegram -->
                                
                                    <button type="button" class="btn btn-success icon" title="Enviar a WhatsApp"
                                        onclick="toggleWhatsAppInput(${orderId}, '${identificadorVenta}')">
                                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                    </button>
                                    <button type="button" class="btn btn-info icon" title="Enviar a Telegram"
                                        onclick="toggleTelegramInput(${orderId}, '${identificadorVenta}')">
                                        <i class="fa-brands fa-telegram"></i> Telegram
                                    </button>
                                </div>

                                <!-- Sección para ingresar número de teléfono de WhatsApp -->
                                <div id="whatsAppSection${orderId}_${identificadorVenta}" class="mt-3" style="display: none;">
                                    <div class="mb-3">
                                        <label for="phone${orderId}_${identificadorVenta}" class="form-label">Número de Teléfono</label>
                                        <input type="text" class="form-control" id="phone${orderId}_${identificadorVenta}" placeholder="Ejemplo: 51987654321" required>
                                        <div class="form-text">Ingrese el número en formato internacional, por ejemplo: 51987654321</div>
                                    </div>
                                    <button type="button" class="btn btn-success" onclick="enviarWhatsApp(${orderId}, '${identificadorVenta}', '${data.identificador_web}')">
                                        <i class="fa-brands fa-whatsapp"></i> Enviar
                                    </button>
                                    <div id="whatsAppResponse${orderId}_${identificadorVenta}" class="mt-2"></div>
                                </div>

                                <!-- Sección para ingresar nombre de usuario de Telegram -->
                                <div id="telegramSection${orderId}_${identificadorVenta}" class="mt-3" style="display: none;">
                                    <div class="mb-3">
                                        <label for="telegramUser${orderId}_${identificadorVenta}" class="form-label">Nombre de usuario de Telegram</label>
                                        <input type="text" class="form-control" id="telegramUser${orderId}_${identificadorVenta}" placeholder="Ejemplo: @usuario">
                                        <div class="form-text">Ingrese el nombre de usuario de Telegram (debe empezar con '@').</div>
                                    </div>
                                    <button type="button" class="btn btn-info" onclick="enviarTelegram(${orderId}, '${identificadorVenta}', '${data.identificador_web}')">
                                        <i class="fa-brands fa-telegram"></i> Enviar
                                    </button>
                                    <div id="telegramResponse${orderId}_${identificadorVenta}" class="mt-2"></div>
                                </div>
                            `;

                            // Cambiar el título del modal a "Detalles de la Venta"
                            document.getElementById('modalTitle').innerText = "Detalles de la Venta";

                            // Abrir el modal
                            abrirModal();
                        },
                        error: function() {
                            console.error('Error al obtener los detalles de la venta.');
                        },
                        complete: function() {
                            // Ocultar el spinner cuando la solicitud finalice
                            hideSpinner();
                        }
                    });
                }

                function abrirModal() {
                    document.getElementById('dynamicModal').style.display = 'flex';
                }

                function cerrarModal() {
                    document.getElementById('dynamicModal').style.display = 'none';
                }

                document.getElementById('closeModal').addEventListener('click', cerrarModal);

                window.onclick = function(event) {
                    const modal = document.getElementById('dynamicModal');
                    if (event.target === modal) {
                        cerrarModal();
                    }
                };

                // Llamar inicialmente para cargar los pedidos
                actualizarPedidos();
            });

            // Función para mostrar u ocultar la sección de WhatsApp
            function toggleWhatsAppInput(orderId, identificadorVenta) {
                const section = $(`#whatsAppSection${orderId}_${identificadorVenta}`);

                // Usamos slideUp para ocultar y slideDown para mostrar con animación
                if (section.is(":visible")) {
                    section.slideUp(); // Si está visible, lo ocultamos
                } else {
                    section.slideDown(); // Si está oculto, lo mostramos
                }
            }

            // Función para enviar los enlaces por WhatsApp
            function enviarWhatsApp(orderId, identificadorVenta, ticketId) {
                const phoneInput = document.getElementById(`phone${orderId}_${identificadorVenta}`);
                let phone = phoneInput.value.trim();
                const responseDiv = document.getElementById(`whatsAppResponse${orderId}_${identificadorVenta}`);

                // Eliminar espacios y signos '+' si están presentes
                phone = phone.replace(/\s+/g, '').replace('+', '');

                // Validar con una expresión regular simple: 10 a 15 dígitos
                const phoneRegex = /^\d{10,15}$/;
                if (!phoneRegex.test(phone)) {
                    responseDiv.innerHTML =
                        '<div class="alert alert-danger">Por favor, ingresa un número de teléfono válido en formato internacional (solo dígitos, sin "+" ni espacios).</div>';
                    return;
                }

                const baseUrl = window.location.origin; // Obtiene el protocolo y dominio
                const ticketUrl = `${baseUrl}/venta/${ticketId}/ticket`;
                const ticketA4Url = `${baseUrl}/venta/${ticketId}/ticket-a4`;

                const mensaje =
                    `¡Hola! Aquí tienes los enlaces de tu venta:\n\n📄 Ticket: ${window.location.origin}/venta/${ticketId}/ticket\n🖨️ Formato A4: ${window.location.origin}/venta/${ticketId}/ticket-a4\n\n¡Gracias por tu compra!`;
                const mensajeEncoded = encodeURIComponent(mensaje);

                const waLink = `https://wa.me/${phone}?text=${mensajeEncoded}`;
                window.open(waLink, '_blank');

                responseDiv.innerHTML = '<div class="alert alert-success">Enlace abierto en WhatsApp.</div>';
                phoneInput.value = '';
                setTimeout(() => {
                    responseDiv.innerHTML = '';
                    toggleWhatsAppInput(orderId, identificadorVenta); // Ocultar sección después de enviar
                }, 3000);
            }

            /// Función para mostrar u ocultar la sección de Telegram
            function toggleTelegramInput(orderId, identificadorVenta) {
                const section = $(`#telegramSection${orderId}_${identificadorVenta}`);

                // Usamos slideUp para ocultar y slideDown para mostrar con animación
                if (section.is(":visible")) {
                    section.slideUp(); // Si está visible, lo ocultamos
                } else {
                    section.slideDown(); // Si está oculto, lo mostramos
                }
            }

            // Función para enviar los enlaces por Telegram
            function enviarTelegram(orderId, identificadorVenta, ticketId) {
                const telegramUserInput = document.getElementById(`telegramUser${orderId}_${identificadorVenta}`);
                let telegramUser = telegramUserInput.value.trim();
                const responseDiv = document.getElementById(`telegramResponse${orderId}_${identificadorVenta}`);

                if (!telegramUser || !telegramUser.startsWith('@')) {
                    responseDiv.innerHTML =
                        '<div class="alert alert-danger">Por favor, ingresa un nombre de usuario válido de Telegram que comience con "@".</div>';
                    return;
                }

                telegramUser = telegramUser.replace(/^@/, '');
                if (/[^a-zA-Z0-9_]/.test(telegramUser)) {
                    responseDiv.innerHTML =
                        '<div class="alert alert-danger">El nombre de usuario solo debe contener letras, números y guiones bajos (_).</div>';
                    return;
                }
                const baseUrl = window.location.origin; // Obtiene el protocolo y dominio
                const ticketUrl = `${baseUrl}/venta/${ticketId}/ticket`;
                const ticketA4Url = `${baseUrl}/venta/${ticketId}/ticket-a4`;

                const mensaje =
                    `¡Hola! Aquí tienes los enlaces de tu venta:\n\n📄 Ticket: ${window.location.origin}/venta/${ticketId}/ticket\n🖨️ Formato A4: ${window.location.origin}/venta/${ticketId}/ticket-a4\n\n¡Gracias por tu compra!`;
                const mensajeEncoded = encodeURIComponent(mensaje);

                const telegramLink = `https://t.me/${telegramUser}?text=${mensajeEncoded}`;
                window.open(telegramLink, '_blank');

                responseDiv.innerHTML = '<div class="alert alert-success">Enlace abierto en Telegram.</div>';
                telegramUserInput.value = '';
                setTimeout(() => {
                    responseDiv.innerHTML = '';
                    toggleTelegramInput(orderId, identificadorVenta); // Ocultar sección después de enviar
                }, 3000);
            }

            function abrirVentanaPDFTicket(url) {
                // Ancho y alto para la ventana emergente
                var width = 400;
                var height = 600;

                // Calcular posición central para la ventana emergente
                var left = (screen.width - width) / 2;
                var top = (screen.height - height) / 2;

                window.open(url, '_blank', `width=${width},height=${height},top=${top},left=${left}`);
            }

            function abrirVentanaPDFA4(url) {
                // Ancho y alto para la ventana emergente
                var width = 800;
                var height = 600;

                // Calcular posición central para la ventana emergente
                var left = (screen.width - width) / 2;
                var top = (screen.height - height) / 2;

                window.open(url, '_blank', `width=${width},height=${height},top=${top},left=${left}`);
            }

            //    cambiar estado  
            function cambiarEstado(ordenId, nuevoEstado) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "El estado se cambiará a " + nuevoEstado,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cambiar!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/ordenes/' + ordenId + '/cambiar-estado',
                            method: 'POST',
                            data: {
                                estado: nuevoEstado,
                                _token: '{{ csrf_token() }}' // Asegúrate de incluir el token CSRF
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Actualizado!',
                                        response.mensaje,
                                        'success'
                                    ).then(() => {
                                        reproducirSonidoSuccess();
                                        setTimeout(function() {
                                            location
                                                .reload(); // Recargar la página después de 2 segundos
                                        }, 2000); // 2000 milisegundos = 2 segundos
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Hubo un problema al actualizar el estado.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }

            // {{-- enviar id de orden para procesarlo --}}
            function procesarOrden(ordenId) {
                // Redirigir al usuario a la página de ventas pasando el codigo_orden de la orden
                window.location.href = `/ventas/create?orden=${ordenId}`;
            }
        </script>
    @endpush

</x-app-layout>
