<!-- Modal para ver venta en ticket -->
<div class="modal fade" id="modalTicket{{ $venta->id }}" tabindex="-1"
    aria-labelledby="modalTicketLabel{{ $venta->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- Puedes ajustar el tamaño según tus necesidades -->
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white" id="modalTicketLabel{{ $venta->id }}">Detalles de la Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                {{-- Mostrar el identificador web --}}
                <p>
                    <strong class="text-dark">Identificador Web:</strong> {{ $venta->identificador_web }}
                    @if (
                        ($venta->comprobante->tipo_comprobante == 'FACTURA' || $venta->comprobante->tipo_comprobante == 'BOLETA') &&
                            !isset($venta->emisionComprobante))
                        <!-- Es FACTURA O BOLETA pero no tienen emision -->
                        <span class="badge {{ $venta->estado == 1 ? 'bg-warning' : 'bg-danger' }}">
                            {{ $venta->estado == 1 ? 'Emitido' : 'Anulado' }}
                        </span>
                    @else
                        <!-- Estado general del venta -->
                        <span class="badge {{ $venta->estado == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $venta->estado == 1 ? 'Emitido' : 'Anulado' }}
                        </span>
                    @endif
                </p>

                {{-- Aquí puedes agregar más detalles de la venta si lo deseas --}}
                <p><strong class="text-dark">Fecha:</strong> {{ $venta->fecha_hora }} <strong
                        class="text-dark">Total:</strong> ${{ number_format($venta->total, 2, '.', ',') }}</p>
                <p><strong class="text-dark">Cliente:</strong> {{ $venta->cliente->persona->razon_social }}</p>

                {{-- Opciones adicionales --}}
                <div class="mt-4">
                    <strong>
                        <h5 class="text-dark">Opciones:</h5>
                    </strong>
                    <div class="d-flex gap-3">
                        {{-- Imprimir en formato Ticket --}}
                        <button type="button" class="btn btn-primary icon" title="Imprimir en formato Ticket"
                            onclick="abrirVentanaPDFTicket('{{ route('venta.abrirVentanaPDFTicket', $venta->identificador_web) }}')">
                            <i class="fa-solid fa-receipt"></i> Ticket
                        </button>

                        {{-- Imprimir en formato A4 --}}
                        <button type="button" class="btn btn-secondary icon" title="Imprimir en formato A4"
                            onclick="abrirVentanaPDFA4('{{ route('venta.abrirVentanaPDFA4', $venta->identificador_web) }}')">
                            <i class="fa-solid fa-file-pdf"></i> A4
                        </button>

                        {{-- Enviar a WhatsApp --}}
                        {{-- @if (!request()->is('empresas')) --}}
                        <button type="button" class="btn btn-success icon" title="Enviar a WhatsApp"
                            data-cliente="{{ $venta->cliente->persona->razon_social }}"
                            onclick="toggleWhatsAppInput({{ $venta->id }})">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                        </button>
                        {{-- @endif --}}

                        <!-- Botón para enviar a Telegram -->
                        {{-- @if (!request()->is('empresas')) --}}
                        <button type="button" class="btn btn-info icon" title="Enviar a Telegram"
                            data-cliente="{{ $venta->cliente->persona->razon_social }}"
                            onclick="toggleTelegramInputNew({{ $venta->id }})">
                            <i class="fa-brands fa-telegram"></i> Telegram
                        </button>
                        {{-- @endif --}}
                    </div>
                </div>

                {{-- Sección para enviar por WhatsApp --}}
                <div id="whatsAppSection{{ $venta->id }}" class="mt-3" style="display: none;">
                    <div class="mb-3">
                        <label for="phone{{ $venta->id }}" class="form-label">Número de Teléfono</label>
                        <input type="number" class="form-control" id="phone{{ $venta->id }}" name="phone"
                            placeholder="51987654321" value="51">
                        <div class="form-text">Ingrese el número en formato internacional, por ejemplo: 51987654321
                        </div>
                    </div>
                    <button type="button" class="btn btn-success"
                        onclick="enviarWhatsApp({{ $venta->id }}, '{{ route('venta.abrirVentanaPDFTicket', $venta->identificador_web) }}', '{{ route('venta.abrirVentanaPDFA4', $venta->identificador_web) }}')">
                        <i class="fa-brands fa-whatsapp"></i> Enviar
                    </button>
                    <div id="whatsAppResponse{{ $venta->id }}" class="mt-2"></div>
                </div>

                <!-- Sección para ingresar el nombre de usuario de Telegram (Nuevo nombre de variables) -->
                <div id="telegramSectionNew{{ $venta->id }}" class="mt-3" style="display: none;">
                    <div class="mb-3">
                        <label for="telegramUserNew{{ $venta->id }}" class="form-label">Nombre de usuario de
                            Telegram</label>
                        <input type="text" class="form-control" id="telegramUserNew{{ $venta->id }}"
                            name="telegramUserNew" placeholder="Ejemplo: @usuario">
                        <div class="form-text">Ingrese el nombre de usuario de Telegram (debe empezar con '@').</div>
                    </div>
                    <button type="button" class="btn btn-info"
                        onclick="enviarTelegramNew({{ $venta->id }}, '{{ route('venta.abrirVentanaPDFTicket', $venta->identificador_web) }}', '{{ route('venta.abrirVentanaPDFA4', $venta->identificador_web) }}')">
                        <i class="fa-brands fa-telegram"></i> Enviar
                    </button>
                    <div id="telegramResponseNew{{ $venta->id }}" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para mostrar u ocultar la sección de WhatsApp
    function toggleWhatsAppInput(ventaId) {
        const section = $(`#whatsAppSection${ventaId}`);
        if (section.is(":visible")) {
            section.slideUp();
        } else {
            section.slideDown();
        }
    }

    // Función para enviar los enlaces por WhatsApp
    function enviarWhatsApp(ventaId, ticketUrl, a4Url) {
        const phoneInput = document.getElementById(`phone${ventaId}`);
        let phone = phoneInput.value.trim();

        const responseDiv = document.getElementById(`whatsAppResponse${ventaId}`);

        // Obtener el nombre del cliente desde el botón
        const clienteNombre = document.querySelector(`button[onclick="toggleWhatsAppInput(${ventaId})"]`).getAttribute(
            'data-cliente');

        // Remover espacios y signos '+'
        phone = phone.replace(/\s+/g, '').replace('+', '');

        // Validación del número
        const phoneRegex = /^\d{10,15}$/;
        if (!phoneRegex.test(phone)) {
            responseDiv.innerHTML =
                '<div class="alert alert-danger">Por favor, ingresa un número de teléfono válido.</div>';
            return;
        }

        const mensaje =
            `¡Hola ${clienteNombre}! Aquí tienes los enlaces de tu venta:\n\n📄 Ticket: ${ticketUrl}\n🖨️ Formato A4: ${a4Url}\n\n¡Gracias por tu compra!`;
        const mensajeEncoded = encodeURIComponent(mensaje);
        const waLink = `https://wa.me/${phone}?text=${mensajeEncoded}`;

        window.open(waLink, '_blank');
        // Mostrar mensaje de éxito
        responseDiv.innerHTML = '<div class="alert alert-success">Enlace abierto en WhatsApp...</div>';

        setTimeout(() => {
            phoneInput.value = '';
            responseDiv.innerHTML = '';
            toggleWhatsAppInput(ventaId);
        }, 3000);
    }


    // Función para mostrar/ocultar la sección de Telegram sin modificar el campo directamente
    function toggleTelegramInputNew(ventaId) {
        const section = $(`#telegramSectionNew${ventaId}`);
        if (section.is(":visible")) {
            section.slideUp(); // Ocultar la sección si ya está visible
        } else {
            section.slideDown(); // Mostrar la sección si está oculta
        }
    }

    // Función para enviar los enlaces por Telegram (Nuevo nombre de función)
    function enviarTelegramNew(ventaId, ticketUrl, a4Url) {
        const telegramUserInput = document.getElementById(`telegramUserNew${ventaId}`);
        let telegramUser = telegramUserInput.value.trim();
        const responseDiv = document.getElementById(`telegramResponseNew${ventaId}`);

        // Obtener el nombre del cliente desde el botón
        const clienteNombre = document.querySelector(`button[onclick="toggleTelegramInputNew(${ventaId})"]`)
            .getAttribute('data-cliente');

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

        const mensaje =
            `¡Hola ${clienteNombre}! Aquí tienes los enlaces de tu venta:\n\n📄 Ticket: ${ticketUrl}\n🖨️ Formato A4: ${a4Url}\n\n¡Gracias por tu compra!`;
        const mensajeEncoded = encodeURIComponent(mensaje);
        const telegramLink = `https://t.me/${telegramUser}?text=${mensajeEncoded}`;

        window.open(telegramLink, '_blank');
        responseDiv.innerHTML = '<div class="alert alert-info">Enlace abierto en Telegram...</div>';

        setTimeout(() => {
            telegramUserInput.value = '';
            responseDiv.innerHTML = '';
            toggleTelegramInputNew(ventaId);
        }, 3000);
    }
</script>

<script>
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
</script>
