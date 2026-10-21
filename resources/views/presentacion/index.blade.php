<x-app-layout>
    @section('title', 'Presentation')

    @push('css')
    @endpush

    <div class="main-content container-fluid">
        <div class="page-title">
            <h1 class="mt-4 text-center text-dark">Presentaciones <i class="fa-solid fa-layer-group fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Presentacion</li>
                        <li class="breadcrumb-item active" aria-current="page">Lista de Presentaciones</li>
                    </ol>
                </nav>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                @can('crear-presentacion')
                    <div class="">
                        <a href="#" class="btn btn-dark round icon" data-bs-toggle="modal"
                            data-bs-target="#crearPresentacionModal">
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
            <div class="card-body px-0 pb-0">
                <div class="table-responsive">
                    <div class="card-body">
                        <table class='table table-hover table-bordered table-striped mb-0' id="table1">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Estado</th>
                                    <th> <i class="fa-solid fa-wrench" style="color: #000000;">...</i></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($presentaciones as $presentacione)
                                    <tr>
                                        <td>
                                            <strong
                                                style="color:black">{{ $presentacione->caracteristica->nombre }}</strong>
                                        </td>
                                        <td><i class="fa-regular fa-comment-dots" style="color:black"></i>
                                            {{ $presentacione->caracteristica->descripcion }}</td>

                                        <td>
                                            @if ($presentacione->caracteristica->estado == 1)
                                                <i class="fa-solid fa-circle-check fa-2xl" style="color: #16ca76;"></i>
                                            @else
                                                <i class="fa-solid fa-circle-xmark fa-2xl" style="color: #a6a6a6;"></i>
                                            @endif

                                        </td>
                                        <td>
                                            <!--  -->
                                            <div class="btn-group" role="group">
                                                <!-----Editar presentacion--->
                                                @can('editar-presentacion')
                                                    @if ($presentacione->caracteristica->nombre !== 'UND' && $presentacione->caracteristica->estado == 1)
                                                        <button type="button" class="btn icon" data-bs-toggle="modal"
                                                            data-bs-target="#editarPresentacionModal"
                                                            data-id="{{ $presentacione->id }}">
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

                                                <!------Eliminar Presentacion---->
                                                @can('eliminar-presentacion')
                                                @if ($presentacione->caracteristica->nombre !== 'UND')
                                                    @if ($presentacione->caracteristica->estado == 1)
                                                        <button type="button" class="btn icon btn"
                                                            onclick="abrirModalConfirmacionPresentacion({{ $presentacione->id }}, 1)">
                                                            <i class="fa-regular fa-trash-can" style="color: #d10000;"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn icon btn-dark"
                                                            onclick="abrirModalConfirmacionPresentacion({{ $presentacione->id }}, 0)">
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

    @can('crear-presentacion')
        <!-- Modal para crear Presentación -->
        <div class="modal fade" id="crearPresentacionModal" tabindex="-1" aria-labelledby="crearPresentacionLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearPresentacionLabel">Registrar Presentación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-crear-presentacion">
                            @csrf
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre - Hasta 4 Caracteres</label>
                                <input type="text" maxlength="4" pattern="[A-Za-z0-9]{1,4}"
                                    title="Por favor, ingresa entre 1 y 4 caracteres que sean letras o números."
                                    class="form-control" name="nombre" id="editarPresentacionNombre" required>
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

    @can('editar-presentacion')
        <!-- Modal para editar Presentacion -->
        <div class="modal fade" id="editarPresentacionModal" tabindex="-1" aria-labelledby="editarPresentacionLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarPresentacionLabel">Editar Presentación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-editar-presentacion">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="id" id="editarPresentacionId">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre - Hasta 4 Caracteres</label>
                                <input type="text" maxlength="4" pattern="[A-Za-z0-9]{1,4}"
                                    title="Por favor, ingresa entre 1 y 4 caracteres que sean letras o números."
                                    class="form-control" name="nombre" id="editarPresentacionNombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarDescripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" name="descripcion" id="editarPresentacionDescripcion"></textarea>
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
    @can('eliminar-presentacion')
        <div class="modal fade" id="confirmModalPresentacion" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Confirmar Acción</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="confirmModalBodyPresentacion"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn-eliminar" id="confirmarEliminacionPresentacion"></button>
                    </div>
                </div>
            </div>
        </div>
    @endcan


    <script>
        document.querySelector('#form-crear-presentacion').addEventListener('submit', async function(e) {
            e.preventDefault();
            // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());
            const storeUrl = '{{ route('presentaciones.store') }}';

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
                    $('#crearPresentacionModal').modal('hide');
                    mostrarToast('success', data.message);
                    reproducirSonidoSuccess();
                    setTimeout(() => location.reload(), 1500); // Recargar la página después de un tiempo
                } else {
                    mostrarToast('error', data.message || 'Ocurrió un error al registrar la presentación.');
                    console.log(data.message);
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

        // rellenar datos actuales antes de editar
        $('#editarPresentacionModal').on('show.bs.modal', async function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            const modal = $(this);
             // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            try {
                const response = await fetch('{{ url('presentaciones') }}/' + id);
                const data = await response.json();

                if (data.success) {
                    modal.find('#editarPresentacionId').val(data.data.id);
                    modal.find('#editarPresentacionNombre').val(data.data.caracteristica?.nombre ||
                        ''); // Usa el operador opcional
                    modal.find('#editarPresentacionDescripcion').val(data.data.caracteristica?.descripcion ||
                        ''); // Usa el operador opcional
                } else {
                    mostrarToast('error', 'Error al cargar los datos de la presentación.');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarToast('error', 'Error de red al cargar la presentación.');
            } finally{
                // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                hideSpinner();
            }
        });

        // Función para enviar la solicitud de editar la presentación
        document.querySelector('#form-editar-presentacion').addEventListener('submit', async function(e) {
            e.preventDefault();
            // Mostrar el spinner al iniciar la solicitud
            showSpinner();
            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData.entries());
            const updateUrl = '{{ url('presentaciones') }}/' + jsonData.id;

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
                    $('#editarPresentacionModal').modal('hide');
                    mostrarToast('success', data.message);
                    reproducirSonidoSuccess();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    mostrarToast('error', data.message || 'Ocurrió un error al actualizar la presentación.');
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
        function abrirModalConfirmacionPresentacion(presentacionId, estado) {
            const modal = $('#confirmModalPresentacion');

            // Mensajes y estilos dinámicos
            const mensajes = {
                1: { texto: '¿Seguro que quieres eliminar esta presentación?', boton: 'Sí, Eliminar', clase: 'btn-danger' },
                0: { texto: '¿Seguro que quieres restaurar esta presentación?', boton: 'Sí, Restaurar', clase: 'btn-warning' }
            };

            $('#confirmModalBodyPresentacion').text(mensajes[estado].texto);
            $('#confirmarEliminacionPresentacion')
                .attr({ 'data-id': presentacionId, 'data-estado': estado })
                .text(mensajes[estado].boton)
                .removeClass('btn-danger btn-warning')
                .addClass(`btn ${mensajes[estado].clase} rounded`);

            modal.modal('show');
        }
        // Cerrar modal al hacer clic en "Cancelar" o "X"
        $(document).on('click', '.btn-close, .btn-secondary', function () {
            document.activeElement.blur(); // Elimina el foco del botón dentro del modal
            setTimeout(() => { 
                $('#confirmModalProducto').modal('hide'); 
            }, 10); // Retrasa un poco el cierre para evitar conflictos con aria-hidden
        });
        
        // Manejo de eliminación/restauración usando AJAX
        $(document).on('click', '#confirmarEliminacionPresentacion', async function (e) {
            e.preventDefault();
            showSpinner();

            const presentacionId = this.getAttribute('data-id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `/presentaciones/${presentacionId}`;

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

                handleEliminarPresentacion(data);
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
                reproducirSonidoAlert();
            } finally {
                hideSpinner();
            }
        });
        // Función para manejar la respuesta de eliminación/restauración
        function handleEliminarPresentacion(data) {
            if (data.success) {
                $('#confirmModalPresentacion').modal('hide');
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
