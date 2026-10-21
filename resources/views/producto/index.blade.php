<x-app-layout>
    @section('title', 'Producto')

    @push('css')
        <!-- Estilos de DataTables -->
        <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

        <!-- Scripts de jQuery y DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <style>
            #productosTableResgiters {
                display: none;
                /* Oculta la tabla inicialmente */
            }
        </style>
    @endpush

    <div class="main-content container-fluid">
        <div class="page-title mb-1">
            <h1 class="mt-4 text-center text-dark">Productos <i class="fa-solid fa-shapes fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Producto</li>
                        <li class="breadcrumb-item active" aria-current="page">Lista de Productos</li>
                    </ol>
                </nav>
            </div>
        </div>
        {{-- <br> --}}
        <div class="text-dark alert alert-secondary alert-dismissible fade show text-center page-title">
            <span>Para que el producto aparezca en el apartado de Ventas, asegúrate de realizar una <a
                    href="{{ route('compras.index') }}" class="text-dark">
                    <strong>Compra</strong></a> previamente.
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </span>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-producto')
                    <div class="mb-2">
                        <a href="#" class="btn btn-dark round icon" data-bs-toggle="modal"
                            data-bs-target="#crearProductoModal">
                            <i class="fas fa-plus"></i>
                        </a>
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
            <div class="card-body">
                <div class="table-responsive">
                    <table class='table table-hover table-striped table-bordered' id="productosTableResgiters">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Marca</th>
                                <th>Presentación</th>
                                <th>Categorías</th>
                                <th>Estado</th>
                                <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $item)
                                <tr>
                                    <td>
                                        <i class="fa-brands fa-slack" style="color:black"></i> {{ $item->codigo }}
                                        <i class="fa {{ $item->destacado == 1 ? 'fa-star' : 'fa-star-o' }}"
                                            style="color: {{ $item->destacado == 1 ? 'blue' : 'black' }};"></i>
                                    </td>
                                    <td>
                                        @if ($item->stock >= 1 && $item->stock <= 5)
                                            <strong style="color:black">{{ $item->nombre }}</strong> <i
                                                class="fa-solid fa-exclamation" style="color:#ffa200"></i>
                                        @elseif ($item->stock <= 0)
                                            <strong style="color:black">{{ $item->nombre }}</strong> <i
                                                class="fa-solid fa-circle-exclamation" style="color:#d10000"></i>
                                        @else
                                            <strong style="color:black">{{ $item->nombre }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="fa-solid fa-mill-sign" style="color:black"></i>
                                        {{ $item->marca->caracteristica->nombre }}
                                    </td>
                                    <td>
                                        <i class="fa-solid fa-weight-scale" style="color:black"></i>
                                        {{ $item->presentacion->caracteristica->nombre }}
                                    </td>
                                    <td>
                                        @foreach ($item->categorias as $category)
                                            <div class="container">
                                                <div class="row">
                                                    <span class="m-1 rounded-pill bg-secondary text-white text-center">
                                                        {{ \Illuminate\Support\Str::limit($category->caracteristica->nombre, 20) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>

                                    <td>
                                        @if ($item->estado == 1)
                                            <i class="fa-solid fa-circle-check fa-2xl" style="color: #16ca76;"></i>
                                        @else
                                            <i class="fa-solid fa-circle-xmark fa-2xl" style="color: #a6a6a6;"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-----Editar Producto--->
                                            @can('editar-producto')
                                                @if ($item->estado == 1)
                                                    <form action="{{ route('productos.edit', ['producto' => $item]) }}"
                                                        method="get">
                                                        @csrf
                                                        <button type="submit" class="btn icon">
                                                            <i class="fa-regular fa-pen-to-square"
                                                                style="color: #52d4ff;"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn icon" disabled>
                                                        <i style="color: #52d4ff;">--</i>
                                                    </button>
                                                @endif
                                            @endcan
                                            <div class="px-1"></div>
                                            <!----Ver-producto--->
                                            <button type="button" class="btn icon btn" data-bs-toggle="modal"
                                                data-bs-target="#verModal"
                                                data-descripcion="{{ $item->descripcion ?? 'No añadido' }}"
                                                data-estado="{{ $item->estado }}" data-stock="{{ $item->stock }}"
                                                data-precio="{{ $item->precioVentaReciente() ? 'S/. ' . $item->precioVentaReciente() : 'No disponible' }}"
                                                data-medidas="{{ $item->medida ?? 'No añadido' }}"
                                                data-fecha_vencimiento="{{ $item->fecha_vencimiento ?? 'No añadido' }}"
                                                data-img_url="{{ $item->img_url ?? 'No añadido' }}"
                                                data-img_path="{{ $item->img_path ? Storage::url('public/productos/' . $item->img_path) : '' }}"
                                                onclick="loadProductDetails(this)">
                                                <i class="fa-solid fa-eye" style="color: #000000;"></i>
                                            </button>

                                            <div class="px-1"></div>
                                            <!----Eliminar y restaurar  producto--->
                                            @can('eliminar-producto')
                                                @if ($item->estado == 1)
                                                    <button type="button" class="btn icon btn"
                                                        onclick="abrirModalConfirmacionProducto({{ $item->id }}, 1)">
                                                        <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn icon btn-dark"
                                                        onclick="abrirModalConfirmacionProducto({{ $item->id }}, 0)">
                                                        <i class="fa-solid fa-arrow-rotate-right"
                                                            style="color: #ffa200;"></i>
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
    </div>
    <!-- Modal vista previa -->
    <div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-dark" id="exampleModalLabel">Detalles del producto</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <p><span class="fw-bolder">Descripción: </span><span id="descripcion">No añadido</span>
                            </p>
                        </div>
                        <div class="col-12">
                            <p><span class="fw-bolder">Stock: </span><span id="stock">0</span></p>
                        </div>
                        <div class="col-12">
                            <p><span class="fw-bolder">Precio: </span><span id="precio">No disponible</span></p>
                        </div>
                        <div class="col-12">
                            <p><span class="fw-bolder">Medidas:<i
                                        class="fa-solid fa-chart-column ms-1 me-1"></i></span><span id="medidas">No
                                    añadido</span></p>
                        </div>
                        <div class="col-12">
                            <p><span class="fw-bolder">Fecha Venc:<i
                                        class="fa-solid fa-calendar-days ms-1 me-1"></i></span><span
                                    id="fecha-vencimiento">No añadido</span></p>
                        </div>
                        <div class="col-12">
                            <p><span class="fw-bolder">Img Url:<i class="fa-solid fa-link ms-1"></i></span><span
                                    id="img-url">No añadido</span></p>
                        </div>
                        <div class="col-12">
                            <span class="fw-bolder">Imagen Subida:<i
                                    class="fa-solid fa-arrow-up-from-bracket ms-1"></i></span>
                            <div class="text-center">
                                <img id="img-path" src="" alt=""
                                    class="img-fluid img-thumbnail border-4 rounded" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @can('crear-producto')
        <!-- Modal para crear Producto -->
        <div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark">Registrar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-dark">
                        <form id="form-crear-producto" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <!-- Código -->
                                {{-- <div class="col-md-3"> --}}
                                {{-- <label for="codigo" class="form-label label-custom">Código:</label> --}}
                                <input readonly type="hidden" class="form-control" name="codigo" id="codigo"
                                    value="{{ $numeroCodigo }}">
                                {{-- </div> --}}
                                <!-- Nombre -->
                                <div class="col-md-7">
                                    <label for="nombre" class="form-label label-custom">Nombre:</label>
                                    <input required type="text" class="form-control" name="nombre" id="nombre"
                                        value="{{ old('nombre') }}">
                                </div>
                                <!-- Stock -->
                                <div class="col-md-3">
                                    <label for="stock" class="form-label label-custom">Stock:</label>
                                    <input required type="number" class="form-control" name="stock" id="stock"
                                        value="{{ old('stock') }}">
                                </div>
                                <!-- Destacado -->
                                <div class="col-md-2">
                                    <label for="destacado" class="form-label">Destacado:</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="destacado" id="destacado" value="1"
                                            class="form-check-input text-primary" {{ old('destacado') }}>
                                        <label class="form-check-label" for="destacado">
                                            Activar
                                        </label>
                                    </div>
                                    @error('destacado')
                                        <small class="text-danger">{{ '*' . $message }}</small>
                                    @enderror
                                </div>
                                <!-- Descripción -->
                                <div class="col-md-6">
                                    <label for="descripcion" class="form-label label-custom">Descripción:</label>
                                    <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{ old('descripcion') }}</textarea>
                                </div>
                                <!-- Medida -->
                                <div class="col-md-3">
                                    <label for="medida" class="form-label label-custom">Medida:
                                        <span style="color:black; font-size: 0.8em;">
                                            (Añade separada por comas ",")
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" name="medida" id="medida"
                                        placeholder="S,M,L,XL,2XL" value="{{ old('medida') }}">
                                </div>
                                <!---Fecha de vencimiento---->
                                <div class="col-md-3">
                                    <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento:</label>
                                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento"
                                        class="form-control" value="{{ old('fecha_vencimiento') }}">
                                </div>
                                <!-- Url img -->
                                <div class="col-md-8">
                                    <label for="img_url" class="form-label label-custom">Url Img:</label>
                                    <input type="text" class="form-control" name="img_url" id="img_url"
                                        placeholder="https://-url-de-imagen" value="{{ old('img_url') }}">
                                </div>
                                <!-- Imagen -->
                                <div class="col-md-4">
                                    <label for="img_path" class="form-label label-custom">Imagen:</label>
                                    <input type="file" class="form-control" name="img_path" id="img_path"
                                        accept="image/*">
                                </div>
                                <!-- Marca -->
                                <div class="col-md-4">
                                    <label for="marca_id" class="form-label">Marca:</label>
                                    <select required name="marca_id" id="marca_id" class="form-control">
                                        @foreach ($marcas as $item)
                                            <option value="{{ $item->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                {{ $item->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Presentación -->
                                <div class="col-md-4">
                                    <label for="presentacione_id" class="form-label label-custom">Presentación:</label>
                                    <select required name="presentacione_id" id="presentacione_id" class="form-control">
                                        @foreach ($presentaciones as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('presentacione_id') == $item->id || $loop->first ? 'selected' : '' }}>
                                                {{ $item->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Categorías -->
                                <div class="col-md-4">
                                    <label for="categorias" class="form-label label-custom">Categorías:</label>
                                    <select required class="form-select" multiple name="categorias[]" id="categorias">
                                        @foreach ($categorias as $item)
                                            <option value="{{ $item->id }}"
                                                {{ in_array($item->id, old('categorias', [])) ? 'selected' : '' }}>
                                                {{ $item->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-regular fa-floppy-disk me-2"></i>Registrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    @can('eliminar-producto')
        <div class="modal fade" id="confirmModalProducto" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Confirmar Acción</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="confirmModalBodyProducto"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn-eliminar" id="confirmarEliminacionProducto"></button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <script>
        // Cargar detalles del producto en el modal de vista previa
        function loadProductDetails(button) {
            // Mostrar el spinner antes de iniciar la carga
            showSpinner();

            try {
                // Obtenemos los datos del botón
                const descripcion = button.getAttribute('data-descripcion');
                const stock = button.getAttribute('data-stock');
                const precio = button.getAttribute('data-precio');
                const medidas = button.getAttribute('data-medidas');
                const fechaVencimiento = button.getAttribute('data-fecha_vencimiento');
                const imgUrl = button.getAttribute('data-img_url');
                const imgPath = button.getAttribute('data-img_path');
                const estado = parseInt(button.getAttribute(
                    'data-estado')); // Asumimos que 'estado' es un valor numérico (1 o 0)

                // Colocamos los datos en el modal
                document.getElementById('descripcion').innerText = descripcion;
                document.getElementById('medidas').innerText = medidas;
                document.getElementById('fecha-vencimiento').innerText = fechaVencimiento;

                // Mostrar stock con clases de colores según las condiciones
                const stockElement = document.getElementById('stock');
                let stockClass = '';

                if (stock >= 1 && stock <= 5) {
                    stockClass = 'bg-warning'; // Amarillo
                } else if (stock <= 0) {
                    stockClass = 'bg-danger'; // Rojo
                } else {
                    stockClass = 'bg-info'; // Azul
                }
                // Actualizamos el stock en el modal con la clase correspondiente
                stockElement.innerHTML = `<span class="badge ${stockClass} badge-pill badge-round ml-1">${stock}</span>`;

                // Verificamos si hay un precio. Si no hay, mostramos el mensaje especial
                const precioElement = document.getElementById('precio');
                if (precio && precio !== 'No disponible') {
                    // Agregar "S/." antes del precio y envolverlo en un span con clase 'bg-dark'
                    precioElement.innerHTML =
                        `<span class="badge bg-dark text-white badge-pill badge-round ms-1">${precio}</span>`;
                } else {
                    // En caso de no tener precio, agregamos el mensaje
                    precioElement.innerHTML = `
                        <span class="badge bg-dark badge-pill badge-round ms-1">No Tiene una Compra</span> 
                        | <a href="{{ route('compras.index') }}"><i class="fa-solid fa-plus"></i> AÑADIR COMPRA</a>`;
                }

                // Si hay una imagen subida (imgPath), la mostramos en su propio espacio
                const imgElement = document.getElementById('img-path');
                if (imgPath) {
                    imgElement.src = imgPath;
                    imgElement.style.width = "200px";
                    imgElement.style.height = "200px";
                    imgElement.style.objectFit = "cover";
                    imgElement.style.display = "block";
                    imgElement.style.marginLeft = "auto";
                    imgElement.style.marginRight = "auto";
                } else {
                    // Si no hay imagen subida, dejamos el espacio vacío
                    imgElement.src = '';
                }

                // Mostrar la imagen de la URL en su propio espacio
                const imgUrlElement = document.getElementById('img-url');
                if (imgUrl) {
                    imgUrlElement.innerHTML =
                        `<img src="${imgUrl}" alt="Imagen del producto" class="img-fluid img-thumbnail border-4 rounded" style="width: 200px; height: 200px; object-fit: cover; display: block; margin-left: auto; margin-right: auto;" />`;
                } else {
                    imgUrlElement.innerHTML = 'No añadido';
                }
            } catch (error) {
                console.error("Error cargando detalles del producto:", error);
            } finally {
                // Ocultar el spinner con un retraso de 0.5 segundos
                setTimeout(() => {
                    if (hideSpinner) hideSpinner();
                }, 500);
            }
        }

        showSpinnerDataTables(); // Mostrar el spinner
        // console.log('Spinner de DataTables mostrado');
        // Mostrar el spinner de DataTables inmediatamente al cargar la página
        document.addEventListener('DOMContentLoaded', function() {


            // Inicializar DataTables
            var table = $('#productosTableResgiters').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthChange": true,
                "order": [],
                "initComplete": function() {
                    // Mostrar la tabla y ocultar el spinner cuando DataTables esté listo
                    setTimeout(function() {
                        $('#productosTableResgiters').css('display',
                            'table'); // Mostrar la tabla
                        hideSpinnerDataTables(); // Ocultar el spinner
                        // console.log('Tabla mostrada y spinner ocultado');
                    }, 1000); // No hay retraso adicional
                },
                "drawCallback": function() {
                    // No es necesario hacer nada aquí para este caso
                }
            });
        });

        $(document).ready(function() {
            // Inicializar Choices.js para los select múltiples si lo estás usando
            if ($('#categorias').length) {
                new Choices('#categorias', {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Selecciona Categorías',
                });
            }
            if ($('#marca_id').length) {
                new Choices('#marca_id', {
                    searchEnabled: true, // Habilita la búsqueda en el select
                    shouldSort: false, // Mantiene el orden original de las opciones
                });
            }

            if ($('#presentacione_id').length) {
                new Choices('#presentacione_id', {
                    searchEnabled: true,
                    shouldSort: false,
                });
            }

            // Manejar el envío del formulario de creación de producto
            $('#form-crear-producto').on('submit', function(e) {
                e.preventDefault();

                // Mostrar el spinner al iniciar la solicitud
                showSpinner();

                const formData = new FormData(this);
                // Verificar si el checkbox "destacado" está marcado
                const destacado = $('#destacado').prop('checked') ? 1 : 0;

                // Añadir el valor de "destacado" al FormData
                formData.append('destacado', destacado);

                // Depuración: Listar todos los pares clave-valor en FormData
                for (let [key, value] of formData.entries()) {
                    // console.log(`${key}:`, value);
                }

                const storeUrl = '{{ route('productos.store') }}';

                $.ajax({
                    url: storeUrl,
                    method: 'POST',
                    data: formData,
                    processData: false, // Importante para enviar FormData
                    contentType: false, // Importante para enviar FormData
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            // Cerrar el modal usando jQuery
                            $('#crearProductoModal').modal('hide');
                            mostrarToast('success', data.message);
                            reproducirSonidoSuccess();
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            mostrarToast('error', data.message ||
                                'Ocurrió un error al registrar el producto.');
                            if (data.errors) {
                                console.log('Errores de validación:', data.errors);
                                mostrarErroresValidacion(data.errors);
                            }
                            reproducirSonidoAlert();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Errores de validación
                            const errors = xhr.responseJSON.errors;
                            mostrarErroresValidacion(errors);
                            mostrarToast('error', 'Hay errores en el formulario.');
                        } else {
                            mostrarToast('error',
                                'Ocurrió un error de red o del servidor. Por favor, intenta nuevamente.'
                            );
                        }
                        reproducirSonidoAlert();
                    },
                    complete: function() {
                        // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                        hideSpinner();
                    }
                });
            });

            // Función Opcional para Mostrar Errores de Validación en el Formulario
            function mostrarErroresValidacion(errors) {
                // Limpiar errores anteriores
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                for (const campo in errors) {
                    const mensajes = errors[campo];
                    const input = $(`[name="${campo}"]`);
                    if (input.length) {
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${mensajes.join('<br>')}</div>`);
                    }
                }
            }

        });


        //FUNCIONES ELIMINACION - RESTAURACION
        function abrirModalConfirmacionProducto(productoId, estado) {
            const modal = $('#confirmModalProducto');

            // Mensajes y estilos dinámicos
            const mensajes = {
                1: {
                    texto: '¿Seguro que quieres eliminar este producto?',
                    boton: 'Sí, Eliminar',
                    clase: 'btn-danger'
                },
                0: {
                    texto: '¿Seguro que quieres restaurar este producto?',
                    boton: 'Sí, Restaurar',
                    clase: 'btn-warning'
                }
            };

            $('#confirmModalBodyProducto').text(mensajes[estado].texto);
            $('#confirmarEliminacionProducto')
                .attr({
                    'data-id': productoId,
                    'data-estado': estado
                })
                .text(mensajes[estado].boton)
                .removeClass('btn-danger btn-warning')
                .addClass(`btn ${mensajes[estado].clase} rounded`);

            modal.modal('show');
        }
        // Cerrar modal al hacer clic en "Cancelar" o "X"
        $(document).on('click', '.btn-close, .btn-secondary', function() {
            document.activeElement.blur(); // Elimina el foco del botón dentro del modal
            setTimeout(() => {
                $('#confirmModalProducto').modal('hide');
            }, 10); // Retrasa un poco el cierre para evitar conflictos con aria-hidden
        });

        // Manejo de eliminación/restauración usando AJAX
        $(document).on('click', '#confirmarEliminacionProducto', async function(e) {
            e.preventDefault();
            showSpinner();

            const productoId = this.getAttribute('data-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `/productos/${productoId}`;

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Error desconocido.');

                handleEliminarProducto(data);
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
                reproducirSonidoAlert();
            } finally {
                hideSpinner();
            }
        });
        // Función para manejar la respuesta de eliminación/restauración
        function handleEliminarProducto(data) {
            if (data.success) {
                $('#confirmModalProducto').modal('hide');
                mostrarToast('success', data.message);
                reproducirSonidoSuccess();
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire('Error', data.message || 'Ocurrió un error.', 'error');
                reproducirSonidoAlert();
            }
        }
    </script>

    @push('js')
    @endpush
</x-app-layout>
