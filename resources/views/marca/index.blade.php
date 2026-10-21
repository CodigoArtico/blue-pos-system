<x-app-layout>

    @section('title', 'marcas')

    @push('css')
    @endpush

    <div class="main-content container-fluid">
        <div class="page-title">
            <h1 class="mt-4 text-center text-dark">Marcas <i class="fa-solid fa-tags fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Marcas</li>
                        <li class="breadcrumb-item active" aria-current="page">Lista de Marcas</li>
                    </ol>
                </nav>
            </div>
        </div>
        <br>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-marca')
                    <a href="#" class="btn btn-dark round icon" data-bs-toggle="modal"
                        data-bs-target="#crearMarcaModal">
                        <i class="fas fa-plus"></i>
                    </a>
                @endcan
                <div class="card-header font-semibold" style="color:black">
                    <i class="fas fa-table me-1"></i>
                    Registros
                </div>
                <div class="d-flex ">
                    <i data-feather="download"></i>
                </div>
            </div>
            <div class="card-body px-0 pb-0">
                <div class="table-responsive">
                    <div class="card-body">
                        <table class='table table-hover table-bordered table-striped' id="table1">
                            <thead>
                                <tr>
                                    <!-- <th>Id</th> -->
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Estado</th>
                                    <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($marcas as $marca)
                                    <tr>

                                        <td>
                                            <strong style="color:black">{{ $marca->caracteristica->nombre }}</strong>
                                        </td>
                                        <td><i class="fa-regular fa-comment-dots" style="color:black"></i>
                                            {{ $marca->caracteristica->descripcion }}</td>

                                        <td>
                                            @if ($marca->caracteristica->estado == 1)
                                                <i class="fa-solid fa-circle-check fa-2xl" style="color: #16ca76;"></i>
                                            @else
                                                <i class="fa-solid fa-circle-xmark fa-2xl" style="color: #a6a6a6;"></i>
                                            @endif

                                        </td>
                                        <td>
                                            <!--  -->
                                            <div class="btn-group" role="group">
                                                <!-----Editar marca--->
                                                @can('editar-marca')
                                                    @if ($marca->caracteristica->nombre !== 'Marca' && $marca->caracteristica->estado == 1)
                                                        <button type="button" class="btn icon" data-bs-toggle="modal"
                                                            data-bs-target="#editarMarcaModal" data-id="{{ $marca->id }}">
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

                                                <!------Eliminar marca---->
                                                @can('eliminar-marca')
                                                    @if ($marca->caracteristica->nombre !== 'Marca')
                                                        @if ($marca->caracteristica->estado == 1)
                                                            <button type="submit" class="btn icon btn"
                                                                onclick="abrirModalConfirmacionMarca({{ $marca->id }}, 1)">
                                                                <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn icon btn-dark"
                                                                onclick="abrirModalConfirmacionMarca({{ $marca->id }}, 0)">
                                                                <i class="fa-solid fa-arrow-rotate-right" style="color: #ffa200;"></i>
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

    @can('crear-marca')
        <!-- Modal para crear Marca -->
        <div class="modal fade" id="crearMarcaModal" tabindex="-1" aria-labelledby="crearMarcaLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearMarcaLabel">Registrar Marca</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-crear-marca">
                            @csrf
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion"></textarea>
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

    @can('editar-marca')
        <!-- Modal para editar Marca -->
        <div class="modal fade" id="editarMarcaModal" tabindex="-1" aria-labelledby="editarMarcaLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarMarcaLabel">Editar Marca</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-marca">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" id="editarMarcaId">
                            <div class="mb-3">
                                <label for="editarNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" id="editarNombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarDescripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" id="editarDescripcion"></textarea>
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

    <!-- Modal de Confirmación para Marcas -->
    @can('eliminar-marca')
        <div class="modal fade" id="confirmModalMarca" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Confirmar Acción</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="confirmModalBodyMarca"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn-eliminar" id="confirmarEliminacionMarca"></button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <script>
        // FUNCION CREAR MARCA
        document.querySelector('#form-crear-marca').addEventListener('submit', async function(e) {
            e.preventDefault();
            // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());
            const storeUrl = '{{ route('marcas.store') }}';

            try {
                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(jsonData)
                });

                const data = await response.json();

                if (data.success) {
                    $('#crearMarcaModal').modal('hide');
                    mostrarToast('success', data.message);
                    reproducirSonidoSuccess();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('error', data.message || 'Ocurrió un error al registrar la marca.');
                    reproducirSonidoAlert();
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                reproducirSonidoAlert();
            } finally{
            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                hideSpinner();
            }
        });

        
        // FUNCION LLENADO DEL MODAL DE EDICION
        $('#editarMarcaModal').on('show.bs.modal', async function(event) {
            const button = $(event.relatedTarget); // Botón que activó el modal
            const id = button.data('id'); // Obtener el ID de la marca

            const modal = $(this);

            // Mostrar el spinner al iniciar la solicitud
            showSpinner();

            try {
                // Hacer la solicitud al backend para obtener los datos
                const response = await fetch(`/marcas/${id}`);
                const data = await response.json();

                if (data.success) {
                    // Llenar los campos del modal con los datos obtenidos
                    modal.find('#editarMarcaId').val(data.data.id);
                    modal.find('#editarNombre').val(data.data.caracteristica?.nombre || '');
                    modal.find('#editarDescripcion').val(data.data.caracteristica?.descripcion || '');
                } else {
                    mostrarToast('error', 'Error al cargar los datos de la marca.');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarToast('error', 'Error de red al cargar la marca.');
            } finally {
                // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                hideSpinner();
            }
        });
        
        // FUNCION EDITAR MARCA
        document.querySelector('#form-editar-marca').addEventListener('submit', async function(e) {
            e.preventDefault();
            // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());
            const updateUrl = '{{ url('marcas') }}/' + jsonData.id;

            try {
                const response = await fetch(updateUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nombre: jsonData.nombre,
                        descripcion: jsonData.descripcion,
                    })
                });

                const data = await response.json();

                if (data.success) {
                    $('#editarMarcaModal').modal('hide');
                    mostrarToast('success', data.message);
                    reproducirSonidoSuccess();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('error', data.message || 'Ocurrió un error al actualizar la marca.');
                    reproducirSonidoAlert();
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarToast('error', 'Ocurrió un error de red. Por favor, intenta nuevamente.');
                reproducirSonidoAlert();
            } finally{
                // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                hideSpinner();
            }
        });


        //FUNCIONES ELIMINACION - RESTAURACION
        function abrirModalConfirmacionMarca(marcaId, estado) {
            const modal = $('#confirmModalMarca');

            // Mensajes y estilos dinámicos
            const mensajes = {
                1: { texto: '¿Seguro que quieres eliminar esta marca?', boton: 'Sí, Eliminar', clase: 'btn-danger' },
                0: { texto: '¿Seguro que quieres restaurar esta marca?', boton: 'Sí, Restaurar', clase: 'btn-warning' }
            };

            $('#confirmModalBodyMarca').text(mensajes[estado].texto);
            $('#confirmarEliminacionMarca')
                .attr({ 'data-id': marcaId, 'data-estado': estado })
                .text(mensajes[estado].boton)
                .removeClass('btn-danger btn-warning')
                .addClass(`btn ${mensajes[estado].clase} rounded`);

            modal.modal('show');
        }
        // Cerrar modal al hacer clic en "Cancelar" o "X"
        $(document).on('click', '.btn-close, .btn-secondary', () => {
            $('#confirmModalMarca').modal('hide');
        });
        // Manejo de eliminación/restauración usando AJAX
        $(document).on('click', '#confirmarEliminacionMarca', async function (e) {
            e.preventDefault();
            showSpinner();

            const marcaId = this.getAttribute('data-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `/marcas/${marcaId}`;

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

                handleEliminarMarca(data);
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
                reproducirSonidoAlert();
            } finally {
                hideSpinner();
            }
        });
        // Función para manejar la respuesta de eliminación/restauración
        function handleEliminarMarca(data) {
            if (data.success) {
                $('#confirmModalMarca').modal('hide');
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
