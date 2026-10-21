<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('button[data-bs-target="#verModal"]').forEach(button => {
            button.addEventListener("click", function() {
                window.showSpinner(); // Mostrar el spinner antes de empezar

            try {
                const estado = this.getAttribute("data-estado");
                const tipoComprobante = this.getAttribute("data-tipo-comprobante");
                const emisionComprobante = parseInt(this.getAttribute(
                    "data-emision-comprobante"));
                const emisionSunat = this.getAttribute("data-emision-sunat");
                const bajaSunat = this.getAttribute("data-baja-sunat");
                const serie = this.getAttribute("data-serie");
                const correlativo = this.getAttribute("data-correlativo");
                const numeroComprobante = this.getAttribute("data-numero-comprobante");
                const mensajeCDR = this.getAttribute("data-mensaje-cdr");
                const contenidoXML = this.getAttribute(
                "data-xml"); // Decodificar antes de mostrar
                // console.log("XML:", contenidoXML);
                // const sunatEstado = this.getAttribute("data-sunat");
                const cliente = this.getAttribute("data-cliente");
                const vendedor = this.getAttribute("data-vendedor");
                const fecha = this.getAttribute("data-fecha");
                const fecha_asignada = this.getAttribute("data-fecha-asignada");
                const hora = this.getAttribute("data-hora");
                const hora_asignada = this.getAttribute("data-hora-asignada");
                const impuesto = parseFloat(this.getAttribute("data-impuesto"));
                const metodoPago = this.getAttribute("data-metodo-pago");
                const montoRecibido = this.getAttribute("data-monto-recibido");
                const numeroOperacion = this.getAttribute("data-numero-operacion");

                // Obtener productos
                const productos = JSON.parse(this.getAttribute("data-productos"));
                const productosLibres = JSON.parse(this.getAttribute("data-productos-libres"));

                let estadoHTML = `
                            <span title="Estado" id="icon-form" class="input-group-text">
                                <i class="fa-solid fa-bell"></i>
                            </span>`;

                if ((tipoComprobante === "FACTURA" || tipoComprobante === "BOLETA") && !
                    emisionComprobante) {
                    if (estado == 1) {
                        estadoHTML += `
                            <input disabled type="text" class="form-control" value="Estado: Activo">
                            <span class="input-group-text"><span class="badge bg-warning">Rechazado</span></span>`;
                    } else {
                        estadoHTML += `
                            <input disabled type="text" class="form-control" value="Estado: Anulado">
                            <span class="input-group-text"><span class="badge bg-danger">Eliminado</span></span>`;
                    }
                } else {
                    if (estado == 1) {
                        estadoHTML += `
                            <input disabled type="text" class="form-control" value="Estado: Activo">
                            <span class="input-group-text"><span class="badge bg-success">Entregado</span></span>`;
                    } else {
                        estadoHTML += `
                            <input disabled type="text" class="form-control" value="Estado: Anulado">
                            <span class="input-group-text"><span class="badge bg-danger">Eliminado</span></span>`;
                    }
                }

                // Construir el Tipo de Comprobante
                let tipoComprobanteHTML =
                    `
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="input-group" id="hide-group">
                                        <span class="input-group-text"><i class="fa-solid fa-file"></i></span>
                                        <input disabled type="text" class="form-control" value="Tipo de comprobante: ${tipoComprobante}">`;

                if (estado == 1 && emisionComprobante) {
                    tipoComprobanteHTML +=
                        `
                                <span class="input-group-text">${emisionSunat} | <i class="fa-solid fa-check-to-slot text-success"></i></span>`;
                } else if (estado == 0 && emisionComprobante && bajaSunat) {
                    tipoComprobanteHTML +=
                        `
                                <span class="input-group-text">Baja: ${bajaSunat} | <i class="fa-regular fa-circle-xmark text-warning"></i></span>`;
                }

                tipoComprobanteHTML += `</div></div></div>`;

                let numeroComprobanteHTML = `
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="input-group" id="hide-group">
                                        <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                        <input disabled type="text" class="form-control" value="Número de comprobante: ${serie}-${correlativo}-${numeroComprobante}">
                                    </div>
                                </div>
                            </div>
                        `;

                let sunatHTML = "";
                if ( estado === "1" && (this.getAttribute("data-tipo-comprobante") === "FACTURA" || this
                    .getAttribute("data-tipo-comprobante") === "BOLETA")) {
                    sunatHTML = `
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text"><i class="fa-solid fa-reply-all"></i></span>
                                            <input disabled type="text" class="form-control" value="SUNAT: ${mensajeCDR}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-group" id="hide-group">
                                            <span class="input-group-text">XML</span>
                                            <input id="copy-input" disabled type="text" class="form-control">
                                            <span class="input-group-text">
                                                <i class="fa-regular fa-copy" id="copy-icon" style="cursor:pointer;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            `;
                }

                // Generar tabla de productos
                let productosHTML = '';
                let totalSuma = 0;

                productos.forEach(item => {
                    let subtotal = (item.pivot.cantidad * item.pivot.precio_ventas) -
                        item.pivot.descuento;
                    totalSuma += subtotal;
                    productosHTML += `
                            <tr>
                                <td>${item.nombre}</td>
                                <td>${item.pivot.cantidad}</td>
                                <td>${parseFloat(item.pivot.precio_ventas).toFixed(2)}</td>
                                <td>${parseFloat(item.pivot.descuento).toFixed(2)}</td>
                                <td class="fw-bold text-end">${subtotal.toFixed(2)}</td>
                                                            </tr>`;
                });

                productosLibres.forEach(item => {
                    let subtotal = (item.cantidad * item.precio_venta) - item.descuento;
                    totalSuma += subtotal;
                    productosHTML += `
                            <tr>
                                <td>${item.nombre} <span class="badge bg-info text-dark">Libre</span></td>
                                <td>${item.cantidad}</td>
                                <td>${parseFloat(item.precio_venta).toFixed(2)}</td>
                                <td>${parseFloat(item.descuento).toFixed(2)}</td>
                                <td class="fw-bold text-end">${subtotal.toFixed(2)}</td>
                                                            </tr>`;
                });

                // Calcular IGV y Total
                let igv = totalSuma * (impuesto / 100);
                let totalFinal = totalSuma + igv;

                // Insertar HTML en el modal
                document.getElementById("venta-detalle").innerHTML = `
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        ${estadoHTML}
                                    </div>
                                </div>
                            </div>
                            ${tipoComprobanteHTML}
                            ${numeroComprobanteHTML}
                            ${sunatHTML}
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user-tie"></i></span>
                                        <input disabled type="text" class="form-control" value="Cliente: ${cliente}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                        <input disabled type="text" class="form-control" value="Vendedor: ${vendedor}">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                                        <input disabled type="text" class="form-control" value="Fecha: ${fecha}">
                                        <span class="input-group-text"><span class="badge bg-info">Asig: ${fecha_asignada}</span></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                                        <input disabled type="text" class="form-control" value="Hora: ${hora}">
                                        <span class="input-group-text"><span class="badge bg-info">Asig: ${hora_asignada}</span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                                        <input disabled type="text" class="form-control" value="Método Pago: ${metodoPago}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-hand-holding-dollar"></i></span>
                                        <input disabled type="text" class="form-control" value="${
                                            metodoPago === 'Efectivo' ? `Monto Recibido: ${montoRecibido}` : `Número Operación: ${numeroOperacion}`
                                        }">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="input-group" id="hide-group">
                                        <span class="input-group-text"><i class="fa-solid fa-percent"></i></span>
                                        <input disabled type="text" class="form-control" value="Impuesto: ${impuesto}">
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-4 shadow-sm rounded border-0">
                                <div class="card-header text-dark">
                                    <i class="fas fa-table me-1"></i> Tabla de Detalle de la Venta
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-hover table-borderless align-middle mb-0">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio de venta</th>
                                                    <th>Descuento</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-dark">
                                                ${productosHTML}
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <th colspan="4" class="text-end">Sumas:</th>
                                                    <th class="fw-bold text-end">${totalSuma.toFixed(2)}</th>
                                                </tr>
                                                <tr class="table-light">
                                                    <th colspan="4" class="text-end">IGV:</th>
                                                    <th class="fw-bold text-end">${igv.toFixed(2)}</th>
                                                </tr>
                                                <tr class="table-light">
                                                    <th colspan="4" class="text-end">Total:</th>
                                                    <th class="fw-bold text-end">${totalFinal.toFixed(2)}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;
                    } catch (error) {
                console.error("Error al procesar:", error);
            } finally {
                // Usamos un setTimeout para asegurar que el spinner se haya mostrado por un tiempo
                setTimeout(() => {
                    window.hideSpinner(); // Ocultar el spinner después de un pequeño retardo
                }, 500); // Retardo de 500ms
            }
            });
        });
        // Función para decodificar y limpiar el XML
        function decodeAndCleanXML(text) {
            const textarea = document.createElement("textarea");
            textarea.innerHTML = text;

            // Reemplazar \" por " y eliminar \n (saltos de línea)
            return textarea.value.replace(/\\"/g, '"').replace(/\\n/g, '');
        }

        document.querySelectorAll('.btn.icon.btn').forEach(boton => {
            boton.addEventListener('click', function() {
                // Obtener y limpiar el XML
                const contenidoXML = decodeAndCleanXML(this.getAttribute("data-xml") || "--");
                // console.log("XML Limpiado:", contenidoXML);

                // Insertar el XML en el input solo si existe
                const inputXML = document.getElementById('copy-input');
                if (inputXML) {
                    inputXML.value = contenidoXML;
                } else {
                    // console.warn("No se encontró el input con ID 'copy-input', se omite la asignación de XML.");
                }

                // Agregar funcionalidad para copiar XML solo si ambos elementos existen
                const copyIcon = document.getElementById('copy-icon');
                if (inputXML && copyIcon) {
                    copyIcon.addEventListener('click', function() {
                        inputXML.disabled = false;
                        inputXML.select();
                        document.execCommand('copy');
                        inputXML.disabled = true;
                        mostrarToast('info', 'Texto copiado al portapapeles');
                    });
                } else {
                    // console.warn("No se encontró el ícono de copiar con ID 'copy-icon' o el input 'copy-input'.");
                }

            });
        });
    });
</script>
