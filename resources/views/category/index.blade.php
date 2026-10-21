<x-app-layout>

    @section('title', 'categorias')

    @push('css')
    @endpush

    <div class="main-content container-fluid px-4">
        <div class="page-title">
            <h1 class="mt-4 text-center text-dark">Categorias <i class="fa-solid fa-folder-open fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Categorias</li>
                        <li class="breadcrumb-item active" aria-current="page">Lista de Categorias</li>
                    </ol>
                </nav>
            </div>
            <!-- <p class="text-subtitle text-muted">A good dashboard to display your statistics</p> -->
        </div>
        <br>

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-categoria')
                    <div class="">
                        <button type="button" class="btn btn-dark round icon" data-bs-toggle="modal"
                            data-bs-target="#crearCategoriaModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                @endcan
                <div class="card-header text-base font-semibold" style="color:black">
                    <i class="fas fa-table me-1"></i>
                    Registros
                </div>

                <div class="d-flex">
                    <i data-feather="download"></i>
                </div>
            </div>
            <div class="card-body px-0 pb-0">
                <div class="table-responsive">
                    <div class="card-body">

                        <table class='table table-hover table-bordered table-striped' id="table1">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado / Destacado</th>
                                    <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categorias as $categoria)
                                    <tr>
                                        <td>
                                            <strong
                                                style="color:black">{{ $categoria->caracteristica->nombre }}</strong>
                                        </td>
                                        <td><i class="fa-regular fa-comment-dots" style="color:black"></i>
                                            {{ $categoria->caracteristica->descripcion }}</td>
                                        <td>
                                            @if ($categoria->caracteristica->estado == 1)
                                                <i class="fa-solid fa-circle-check fa-2xl" style="color: #16ca76;"></i>
                                            @else
                                                <i class="fa-solid fa-circle-xmark fa-2xl" style="color: #a6a6a6;"></i>
                                            @endif
                                            <i class="fa {{ $categoria->caracteristica->destacado == 1 ? 'fa-star' : 'fa-star-o' }} ms-2"
                                                style="color: {{ $categoria->caracteristica->destacado == 1 ? 'blue' : 'black' }};"></i>

                                        </td>
                                        <td>
                                            <!--  -->
                                            <div class="btn-group" role="group">

                                                <!-----Editar categoría--->
                                                @can('editar-categoria')
                                                    @if ($categoria->caracteristica->nombre !== 'Variado' && $categoria->caracteristica->estado == 1)
                                                        <button type="button" class="btn icon" data-bs-toggle="modal"
                                                            data-bs-target="#editarCategoriaModal"
                                                            data-id="{{ $categoria->id }}">
                                                            <i class="fa-regular fa-pen-to-square"
                                                                style="color: #52d4ff;"></i>
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn icon" disabled>
                                                            <i style="color: #52d4ff;">--</i>
                                                        </button>
                                                    @endif
                                                @endcan

                                                <div class="px-1"></div>
                                                <!------Eliminar categoria---->
                                                @can('eliminar-categoria')
                                                    @if ($categoria->caracteristica->nombre !== 'Variado')
                                                        @if ($categoria->caracteristica->estado == 1)
                                                            <button type="submit" class="btn icon btn"
                                                                onclick="abrirModalConfirmacion({{ $categoria->id }}, 1)">
                                                                <i class="fa-regular fa-trash-can"
                                                                    style="color: #d10000;"></i>
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn icon btn-dark"
                                                                onclick="abrirModalConfirmacion({{ $categoria->id }}, 0)">
                                                                <i class="fa-solid fa-arrow-rotate-right"
                                                                    style="color: #ffa200;"></i>
                                                            </button>
                                                        @endif
                                                    @else
                                                        <button type="submit" class="btn icon btn" disabled>
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
        </div>
    </div>
    @can('crear-categoria')
        <!-- Modal para crear categoría -->
        <div class="modal fade" id="crearCategoriaModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Crear Nueva Categoría</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('categorias.store') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre"
                                    value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <small class="text-danger">{{ '*' . $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción:</label>
                                <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <small class="text-danger">{{ '*' . $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="destacado" id="destacado"
                                    value="1" {{ old('destacado') ? 'checked' : '' }}>
                                <label class="form-check-label" for="destacado">Destacado</label>
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
    @can('editar-categoria')
        <!-- Modal de edición -->
        <div class="modal fade" id="editarCategoriaModal" tabindex="-1" aria-labelledby="editarCategoriaLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarCategoriaLabel">Editar Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-categoria">
                            <input type="hidden" id="categoria-id" name="id">

                            <div class="mb-3">
                                <label for="nombre-editar" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre-editar" name="nombre" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion-editar" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion-editar" name="descripcion" rows="3"></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="destacado_editar"
                                    id="destacado_editar">
                                <label class="form-check-label" for="destacado_editar">Destacado</label>
                            </div>

                            <div class="text-center">
                                {{-- <button type="button" class="btn btn-secondary icon" data-bs-dismiss="modal" style="margin-right: 160px;">Cerrar</button> --}}
                                <button type="submit" class="btn btn-primary icon"><i class="fa-regular fa-floppy-disk me-2"></i>Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    @can('eliminar-categoria')
        <!-- Modal de confirmación (fuera del foreach) -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Mensaje de Confirmación</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="confirmModalBody">
                        <!-- El mensaje se llenará dinámicamente con JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn-eliminar" id="confirmarEliminacion" data-id=""
                            data-estado="">
                            <!-- El texto del botón se llenará dinámicamente con JavaScript -->
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <script>
        // RUTAS CONSTANTES
        const updateUrl = '{{ route('categorias.update', ['categoria' => 'ID']) }}';
        const csrfToken = '{{ csrf_token() }}'; // CSRF Token global

        // Validación básica del nombre
        function validarNombre(nombre) {
            return nombre.trim() !== '';
        }

        // FUNCION CREAR CATEGORIA
        document.querySelector('#crearCategoriaModal form').addEventListener('submit', function(e) {
            e.preventDefault();
            // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            const nombreInput = this.querySelector('input[name="nombre"]');

            if (!validarNombre(nombreInput.value)) {
                mostrarToast('warning', 'Por favor, ingresa un nombre para la categoría.');
                reproducirSonidoAlert();
                return;
            }

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());

            const storeUrl = '{{ route('categorias.store') }}';

            fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(jsonData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#crearCategoriaModal').modal('hide');
                        reproducirSonidoSuccess();
                        mostrarToast('success', data.message);
                        setTimeout(() => location.reload(), 1500); // Recargar la página después de 1.5 segundos
                    } else {
                        mostrarToast('error', data.message || 'Ocurrió un error al registrar la categoría.');
                        reproducirSonidoAlert();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                    reproducirSonidoAlert();
                })
                .finally(() => {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                });
        });

        // FUNCION LLENAR MODAL DE CATEGORIA
        $('#editarCategoriaModal').on('show.bs.modal', async function(event) {
            const button = $(event.relatedTarget); // Botón que activó el modal
            const id = button.data('id'); // Obtener el ID de la categoría

            const modal = $(this);

            // Mostrar el spinner al iniciar la solicitud
            showSpinner();

            try {
                // Hacer la solicitud al backend para obtener los datos
                const response = await fetch(`/categorias/${id}`);
                const data = await response.json();

                if (data.success) {
                    // Llenar los campos del modal con los datos obtenidos
                    modal.find('#categoria-id').val(data.data.id);
                    modal.find('#nombre-editar').val(data.data.caracteristica?.nombre || '');
                    modal.find('#descripcion-editar').val(data.data.caracteristica?.descripcion || '');
                    modal.find('#destacado_editar').prop('checked', data.data.caracteristica?.destacado == 1);
                } else {
                    mostrarToast('error', 'Error al cargar los datos de la categoría.');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarToast('error', 'Error de red al cargar la categoría.');
            } finally {
                // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                hideSpinner();
            }
        });

        // FUNCION EDITAR CATEGORIA
        document.querySelector('#form-editar-categoria').addEventListener('submit', function(e) {
            e.preventDefault();
            // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            const categoriaId = document.getElementById('categoria-id').value;
            const nombre = document.getElementById('nombre-editar').value;
            const descripcion = document.getElementById('descripcion-editar').value;
            const destacado = document.getElementById('destacado_editar').checked ? 1 : 0;

            fetch(`/categorias/${categoriaId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nombre,
                        descripcion,
                        destacado
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Usamos un toast en lugar de un Swal grande
                        mostrarToast('success', data.message);
                        reproducirSonidoSuccess();
                        $('#editarCategoriaModal').modal('hide');
                        setTimeout(() => location.reload(), 1500); // Recargar la página después de 1.5 segundos
                    } else {
                        mostrarToast('error', data.message || 'Ocurrió un error al actualizar la categoría.');
                        reproducirSonidoAlert();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarToast('error', 'Ocurrió un error al actualizar la categoría.');
                    reproducirSonidoAlert();
                }).finally(() => {
                    // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                    hideSpinner();
                });
        });

        //FUNCIO ELIMINAR - RESTAURAR 
        function abrirModalConfirmacion(categoriaId, estado) {
            const modal = $('#confirmModal');

            // Objeto con mensajes dinámicos
            const mensajes = {
                1: { 
                    texto: '¿Seguro que quieres eliminar esta categoría?', 
                    boton: 'Sí, Eliminar', 
                    clase: 'btn-danger' 
                },
                0: { 
                    texto: '¿Seguro que quieres restaurar esta categoría?', 
                    boton: 'Sí, Restaurar', 
                    clase: 'btn-warning' 
                }
            };

            // Configurar el contenido del modal
            $('#confirmModalBody').text(mensajes[estado].texto);
            $('#confirmarEliminacion')
                .attr({ 'data-id': categoriaId, 'data-estado': estado })
                .text(mensajes[estado].boton)
                .removeClass('btn-danger btn-warning')
                .addClass(`btn ${mensajes[estado].clase} rounded`);

            // Mostrar el modal
            modal.modal('show');
        }
        // Cerrar modal al hacer clic en "Cancelar" o "X"
        $(document).on('click', '.btn-close, .btn-secondary', () => {
            $('#confirmModal').modal('hide');
        });
        // Manejo de eliminación/restauración usando AJAX
        $(document).on('click', '#confirmarEliminacion', async function (e) {
            e.preventDefault();
            showSpinner(); // Mostrar spinner

            const categoriaId = this.getAttribute('data-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `/categorias/${categoriaId}`;

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

                handleEliminarCategoria(data, categoriaId);
            } catch (error) {
                console.error('Error en la solicitud:', error.message);
                Swal.fire('Error', error.message, 'error');
                reproducirSonidoAlert();
            } finally {
                hideSpinner(); // Ocultar spinner
            }
        });
        // Función para manejar la respuesta de eliminación/restauración
        function handleEliminarCategoria(data, categoriaId) {
            if (data.success) {
                $('#confirmModal').modal('hide'); 
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
