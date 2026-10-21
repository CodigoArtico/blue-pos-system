<x-app-layout>
    @section('title', 'Detalles de la Mesa')

    @push('css')
        @include('mesas.partials.css')
    @endpush
    <script>
        function procesarVentaMesa(mesaId) {
            // console.log("CODIGO de la mesa:", mesaId); // Verifica el valor aquí
            window.location.href = `/ventas/create?mesa=${mesaId}`;
        }

        function obtenerDatos() {
            return productos.map(producto => ({
                producto_id: producto.producto_id,
                cantidad: document.getElementById(`cantidad_${producto.producto_id}`).value,
                precioVentaReciente: producto.precioVentaReciente
            }));
        }
    </script>
    <div class="container-fluid px-4">
        <h1 class="font-weight-bold text-dark text-center">Detalles: {{ $mesa->nombre }}</h1>
        <input type="hidden" id="mesa_id" value="{{ $mesa->id }}">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="{{ route('mesas.index') }}">Mesas</a></li>
            <li class="breadcrumb-item active">Detalle de Mesa</li>
            <li class="breadcrumb-item active"><a href="{{ route('ventas.index') }}">Ir- Ventas</a></li>
        </ol>
        <div class="row">
            <!-- Detalles de la Mesa -->
            <div class="col-md-12">
                <div class="card border-0 shadow-lg rounded-lg p-4 mb-4">
                    <div
                        class="alert alert-light text-center border {{ $mesa->estado == 0 ? 'border-success' : 'border-danger' }} rounded p-3">
                        <p class="mb-0 font-weight-bold">
                            <strong class="text-dark">Estado:</strong>
                            <span class="{{ $mesa->estado == 0 ? 'text-success' : 'text-danger' }}">
                                <i
                                    class="{{ $mesa->estado == 0 ? 'fa fa-check-circle' : 'fa fa-times-circle' }} mr-2"></i>
                                {{ $mesa->estado == 0 ? 'Libre' : 'Ocupada ' }}
                            </span>
                            @if ($mesa->reserva == 1)
                                | <i class="fa-solid fa-person-walking-dashed-line-arrow-right text-warning"></i>
                                <strong class="text-warning">Reservada </strong>|
                                <i class="fa-solid fa-user-tie text-dark"></i> <strong
                                    class="text-dark">{{ $mesa->nombre_reserva }}</strong>
                                <i class="fa-solid fa-clock text-dark"></i> <strong
                                    class="text-dark">{{ $mesa->hora_reserva }}</strong>
                            @endif
                        </p>
                    </div>
                    <hr>
                    <h3 class="font-weight-bold text-dark mb-4 text-center">Agregar Producto a la Mesa</h3>

                    <div class="row align-items-end">
                        <div class="col-md-10 mb-3">
                            <label for="producto_id" class="font-weight-bold text-dark">Seleccione un Producto:</label>
                            <select name="producto_id" id="producto_id" class="form-control select2"
                                title="Busque un producto aquí">
                                <option value="" disabled selected>Seleccione un producto</option>  <!-- Opción vacía al principio -->
                                @foreach ($productos as $item)
                                    <option
                                        value="{{ $item->id }}-{{ $item->stock }}-{{ $item->precioVentaReciente() }}">
                                        {{ $item->nombre .  ' - ' . $item->codigo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3 d-flex">
                            <button id="btn-add-product"
                                class="btn icon btn-primary btn-lg w-100 h-100 shadow-sm d-flex justify-content-center align-items-center">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row justify-content-center py-0">
                        <div class="col-md-8 mb-0">
                            <div id="producto-info" style="font-size: 16px;"
                                class="alert alert-dark d-flex align-items-center font-weight-bold py-2 text-center d-none"
                                role="alert">
                                <!-- Aquí se mostrará la información del producto -->
                            </div>
                        </div>
                    </div>

                    <hr class="my-1 text-dark">
                    <p class="text-muted text-center">Seleccione un producto y haga clic en " + ".</p>
                    <p class="text-muted text-center">Asegúrate de haber asignado un precio al producto.</p>
                    <div class="text-center mt-0">
                        @if (!$mesa->productos->isEmpty())
                            <button onclick="procesarVentaMesa({{ json_encode($mesa->codigo_mesa) }})"
                                title="Procesar productos" type="button" class="btn btn-success btn-lg shadow-sm">
                                <i class="fa fa-check-circle mr-2"></i> Procesar Venta
                            </button>
                        @else
                            <button disabled title="Añade Productos a la Mesa" type="button"
                                class="btn btn-light btn-lg shadow-sm">
                                <i class="fa fa-check-circle mr-2"></i> Procesar Venta
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- obtner los productos y almacenarlos  --}}
        <script>
            const productos = [];
        </script>
        <h3 class="mt-4 text-dark">Productos Añadidos</h3>
        <div class="row flex-nowrap overflow-auto" id="productos-carrito"
            style="white-space: nowrap; scroll-behavior: smooth;">
            @foreach ($mesa->productos as $item)
                <div class="col-auto">
                    <div class="card border-0 shadow-sm rounded-lg mx-o" id="producto-{{ $item->id }}"
                        style="min-width: 300px;">
                        @if ($item->img_path)
                            <img src="{{ Storage::url('public/productos/' . $item->img_path) }}"
                                alt="{{ $item->nombre }}" class="card-img-top"
                                style="height: 180px; object-fit: cover;">
                        @else
                            <img src="{{ $item->img_url }}" alt="{{ $item->nombre }}" class="card-img-top"
                                style="height: 180px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold text-center text-dark">
                                {{ \Illuminate\Support\Str::limit($item->nombre, 18, '...') }}</h5>
                            <p class="card-text text-primary font-weight-bold text-center">
                                S/ <span
                                    id="precio_{{ $item->id }}">{{ number_format($item->precioVentaReciente(), 2) }}</span>
                            </p>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <label for="cantidad_{{ $item->id }}" class="mr-2 text-dark">Cantidad:</label>
                                <input type="number" id="cantidad_{{ $item->id }}"
                                    name="cantidad[{{ $item->id }}]" value="{{ $item->pivot->cantidad }}"
                                    min="1" class="form-control w-25 text-center"
                                    oninput="calcularTotal(); actualizarCantidad({{ $item->id }}); obtenerDatos();">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="button"
                                    onclick="actualizarCantidad({{ $item->id }}); obtenerDatos();"
                                    class="btn btn-outline-primary">Actualizar</button>
                                <button onclick="eliminarProducto({{ $item->id }})"
                                    class="btn btn-outline-danger icon">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    productos.push({
                        producto_id: {{ $item->id }},
                        cantidad: {{ $item->pivot->cantidad }},
                        precioVentaReciente: {{ $item->precioVentaReciente() }}
                    });
                </script>
            @endforeach
        </div>

        <!-- Monto Total a Pagar -->
        <div class="row mt-4">
            <div class="col-md-12 text-right">
                <div class="alert alert-light border border-primary rounded p-3 shadow-sm">
                    <h4 class="mb-0">Total a Pagar:
                        <span id="total_a_pagar" class="text-primary font-weight-bold">S/ 0.00</span>
                    </h4>
                </div>
            </div>
        </div>

    </div>

    @push('js')
        <script>
            // mostrar datos del producto seleccionado 
            $(document).ready(function() {
                calcularTotal(); // Calcular el total al cargar la página
                // Inicializar Select2
                $('#producto_id').select2({
                    placeholder: "Selecciona un producto",
                    allowClear: true,
                    width: '100%'
                }); // Aplica select2 en el select con id 'producto_id'

                // Evento de cambio con select2:select
                $('#producto_id').on('select2:select', function(e) {
                    // Obtenemos el valor seleccionado del select
                    var valorSeleccionado = e.params.data.id; // El valor completo (id-stock-precio)

                    // console.log('Valor seleccionado: ', valorSeleccionado);

                    if (valorSeleccionado) {
                        var partes = valorSeleccionado.split('-');
                        if (partes.length < 3) {
                            console.error('Valor del producto mal formado: ',
                                valorSeleccionado); // Si el valor no tiene las tres partes, lo indicamos
                            return;
                        }

                        var stock = partes[1]; // Stock
                        var precioVenta = partes[2]; // Precio de venta

                        // console.log('Stock: ', stock); // Verifica si el stock es correcto
                        // console.log('Precio de venta: ', precioVenta); // Verifica si el precio es correcto

                        // Actualizamos el contenido del div de información
                        $('#producto-info').text('Precio de Venta: $' + precioVenta + ' | Stock: ' + stock);

                        // Mostramos el div
                        $('#producto-info').removeClass('d-none');
                    } else {
                        // Ocultamos el div si no hay selección
                        $('#producto-info').addClass('d-none');
                    }
                });
            });

            // Agregar producto a la mesa
            document.getElementById('btn-add-product').addEventListener('click', function() {
                const select = document.getElementById('producto_id');
                const selectedValue = select.value;
                const [productoId, stock, precio] = selectedValue.split('-');

                // Verificar que se haya seleccionado un producto
                if (!productoId) {
                    reproducirSonidoAlert();
                    mostrarToast('warning', 'Selecciona un Producto'); // Muestra el mensaje con el icono 'danger'
                    return;
                }

                // Envío del producto como JSON
                fetch('{{ route('mesas.addProduct', $mesa->id) }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            producto_id: productoId,
                            cantidad: 1, // O puedes permitir que el usuario elija la cantidad
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            reproducirSonidoSuccess();
                            // Muestra el mensaje informativo o de error basado en la respuesta
                            mostrarToast(data.icon, data.message); // Usa el icono que recibes
                            calcularTotal(); // Actualiza el total
                            // Espera 2 segundos antes de actualizar el total y recargar la página
                            setTimeout(() => {
                                location.reload(); // Recarga la página para ver la nueva tarjeta
                            }, 1500); // 2000 milisegundos = 2 segundos
                        } else {
                            reproducirSonidoAlert();
                            // Si hay un error, puedes mostrar un mensaje alternativo
                            // Muestra el mensaje informativo o de error basado en la respuesta
                            mostrarToast(data.icon, data.message); // Usa el icono que recibes
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });

            // eliminar producto de la mesa 
            function eliminarProducto(productoId) {
                fetch(`{{ url('/mesas/' . $mesa->id . '/productos') }}/${productoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            reproducirSonidoSuccess();
                            // Muestra el mensaje informativo o de error basado en la respuesta
                            mostrarToast(data.icon, data.message); // Usa el icono que recibes
                            calcularTotal(); // Actualiza el total
                            // Espera 2 segundos antes de actualizar el total y recargar la página
                            setTimeout(() => {
                                location.reload(); // Recarga la página para ver la nueva tarjeta
                            }, 1500); // 1,5 segundos
                        } else {
                            // Si hay un error, puedes mostrar un mensaje alternativoesta
                            mostrarToast(data.icon, data.message); // Usa el icono que recibes
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            // Actualizar cantidad de productos de la mesa
            function actualizarCantidad(productoId) {
                const cantidadInput = document.getElementById(`cantidad_${productoId}`);
                let cantidad = cantidadInput.value.trim(); // Obtenemos el valor del input

                // Si el campo está vacío, no hacemos nada
                if (cantidad === "") {
                    return; // No actualizar, solo retorna
                }

                // Si la cantidad es menor que 1 (por ejemplo 0 o número negativo), la corregimos a 1
                if (cantidad <= 0 || isNaN(cantidad)) {
                    cantidad = 1; // O puedes poner 0 si prefieres eliminar el producto
                    cantidadInput.value = cantidad; // Actualiza el input con el valor corregido
                }

                if (cantidad > 0) {
                    fetch(`{{ url('/mesas/' . $mesa->id . '/productos') }}/${productoId}`, {
                            method: 'PUT',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                cantidad
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                reproducirSonidoSuccess();
                                // Muestra el mensaje informativo o de error basado en la respuesta
                                mostrarToast(data.icon, data.message); // Usa el icono que recibes
                                calcularTotal(); // Actualiza el total
                            } else {
                                // Si hay un error, puedes mostrar un mensaje alternativo
                                mostrarToast(data.icon, data.message); // Usa el icono que recibes
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            }

            function calcularTotal() {
                let total = 0;

                // Crear un arreglo para almacenar las cantidades
                const cantidades = [];

                // Iterar sobre todos los productos en el carrito
                @foreach ($mesa->productos as $item)
                    // Utiliza let o const para evitar conflictos de nombre
                    const cantidad{{ $item->id }} = parseInt(document.getElementById('cantidad_{{ $item->id }}')
                        .value) || 0;
                    const precio{{ $item->id }} = parseFloat({{ $item->precioVentaReciente() }}) ||
                        0; // Obtener precio directamente

                    total += cantidad{{ $item->id }} * precio{{ $item->id }};
                @endforeach

                // Mostrar el total actualizado
                document.getElementById('total_a_pagar').textContent = `S/ ${total.toFixed(2)}`;
            }
        </script>
    @endpush

</x-app-layout>
