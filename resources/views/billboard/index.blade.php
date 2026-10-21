<x-app-layout>
    @section('title', 'Gestión Billboard')

    @push('css')
        <style>
            .form-label {
                font-weight: bold;
            }

            .card-header {
                background-color: #000000;
                color: white;
                font-size: 1.2rem;
            }

            .card {
                border: none;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .modal-header {
                background-color: #000000;
                color: white;
            }

            .modal-body img {
                border-radius: 5px;
                max-height: 250px;
            }

            .modal-header .btn-close {
                filter: invert(1);
                /* Cambia el color del ícono */
                background-color: transparent;
                /* Asegura que no haya fondo */
                border: none;
                /* Opcional, elimina bordes si aparecen */
            }

            .modal-header .btn-close:hover {
                filter: invert(0.8);
                /* Color más claro al pasar el cursor (opcional) */
            }
        </style>
    @endpush

    <div class="main-content container-fluid py-4">
        <div class="page-title">
            <h1 class="text-center text-dark">Billboard <i class="fa-solid fa-pager fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Billboards</li>
                    </ol>
                </nav>
            </div>
            <!-- <p class="text-subtitle text-muted">A good dashboard to display your statistics</p> -->
        </div>
        <div class="text-dark alert alert-secondary alert-dismissible fade show text-center page-title">
            <span>"Desde esta plataforma, podrás gestionar tu sitio web, incluyendo las imágenes principales y otros
                elementos relevantes."
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </span>
        </div>
        <div class="card mt-1">
            {{-- <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Gestión de Billboard
            </div> --}}

            <div class="container-fluid text-dark">
                @can('editar-billboard')
                    <form id="updateBillboardForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <br>
                        <!-- detalles del sitio -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-2">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="{{ old('nombre', $pageWeb->nombre) }}" required>
                            </div>
                            <div class="col-md-2">
                                <label for="telefono" class="form-label">Telefono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono"
                                    placeholder="Ejem: +51987654321" value="{{ old('telefono', $pageWeb->telefono) }}"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="url_sistem" class="form-label">Url Sistema</label>
                                <input type="text" class="form-control" id="url_sistem" name="url_sistem"
                                    value="{{ old('url_sistem', $pageWeb->url_sistem ?: request()->getSchemeAndHttpHost()) }}"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="url_web" class="form-label">Url Pagina Web</label>
                                <input type="text" class="form-control" id="url_web" name="url_web"
                                    value="{{ old('url_web', $pageWeb->url_web) }}" required>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ $pageWeb->url_web }}" target="_blank" class="btn icon btn-dark mt-4">
                                    <i class="fa-solid fa-globe fa-bounce"></i> Mi Pagina
                                </a>
                            </div>
                        </div>

                        <!-- Descripción del sitio -->
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion', $pageWeb->descripcion) }}</textarea>
                            </div>
                        </div>

                        <div class="container-fluid">
                            <!-- Billboards Section -->
                            <h5 class="mb-4 text-dark text-center">Billboards</h5>
                            <div class="row justify-content-center">
                                @foreach ($pageWeb->billboards as $billboard)
                                    <div class="col-12 col-lg-8">
                                        <div class="row g-3 mb-4 border p-3 rounded shadow-sm">
                                            <!-- Campo de URL -->
                                            <div class="col-12 col-md-6">
                                                <label for="img_url_{{ $billboard->id }}" class="form-label">Imagen URL (ID:
                                                    {{ $billboard->id }})</label>
                                                <input type="text" class="form-control" id="img_url_{{ $billboard->id }}"
                                                    name="img_url[]"
                                                    value="{{ old('img_url.' . $loop->index, $billboard->img_url) }}">
                                            </div>

                                            <!-- Activar/Desactivar URL -->
                                            <div class="col-12 col-md-6 d-flex align-items-center">
                                                <label for="estado_url_{{ $billboard->id }}"
                                                    class="form-label me-2">Activar URL:</label>
                                                <input type="hidden" name="estado_url[{{ $loop->index }}]"
                                                    value="0">
                                                <input type="checkbox" id="estado_url_{{ $billboard->id }}"
                                                    name="estado_url[{{ $loop->index }}]" value="1"
                                                    {{ $billboard->estado_url ? 'checked' : '' }}>
                                            </div>

                                            <!-- Campo de Path -->
                                            <div class="col-12 col-md-6">
                                                <label for="img_path_{{ $billboard->id }}" class="form-label">Seleccionar
                                                    nueva imagen</label>
                                                <input type="file" class="form-control"
                                                    id="img_path_{{ $billboard->id }}" name="img_path[]">
                                            </div>

                                            <!-- Activar/Desactivar Path -->
                                            <div class="col-12 col-md-6 d-flex align-items-center">
                                                <label for="estado_path_{{ $billboard->id }}"
                                                    class="form-label me-2">Activar Path:</label>
                                                <input type="hidden" name="estado_path[{{ $loop->index }}]"
                                                    value="0">
                                                <input type="checkbox" id="estado_path_{{ $billboard->id }}"
                                                    name="estado_path[{{ $loop->index }}]" value="1"
                                                    {{ $billboard->estado_path ? 'checked' : '' }}>
                                            </div>

                                            <!-- Botón para ver la imagen -->
                                            @can('mostrar-billboard')
                                                <div class="col-12 text-center mt-3">
                                                    <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                                        data-bs-target="#verModal-{{ $billboard->id }}">
                                                        <i class="fas fa-eye"></i> Ver imágenes...
                                                    </button>
                                                </div>
                                            @endcan

                                            <!-- Modal de vista previa de la imagen -->
                                            <div class="modal fade" id="verModal-{{ $billboard->id }}" tabindex="-1"
                                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Vista previa de la imagen</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <h6>Imagen subida local:</h6>
                                                            @if ($billboard->img_path)
                                                                <img src="{{ $billboard->getImageUrlAttribute() }}"
                                                                    class="img-fluid mb-3" alt="Imagen almacenada">
                                                            @else
                                                                <p>No hay imagen cargada.</p>
                                                            @endif

                                                            <h6>Imagen de URL:</h6>
                                                            @if ($billboard->img_url)
                                                                <img src="{{ $billboard->img_url }}" class="img-fluid"
                                                                    alt="Imagen desde URL">
                                                            @else
                                                                <p>No hay URL de imagen proporcionada.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Botón de actualización -->
                        <div class="text-center mb-2">
                            <button type="submit" class="btn btn-primary mt-3 text-end">Actualizar</button>
                        </div>
                    </form>
                @else
                    <form action="#">
                        <br>
                        <!-- Detalles del sitio -->
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-2">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input disabled type="text" class="form-control" id="nombre" name="nombre"
                                    value="{{ old('nombre', $pageWeb->nombre) }}" required>
                            </div>
                            <div class="col-md-2">
                                <label for="telefono" class="form-label">Telefono</label>
                                <input disabled type="text" class="form-control" id="telefono" name="telefono"
                                    placeholder="Ejem: +51987654321" value="{{ old('telefono', $pageWeb->telefono) }}"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="url_sistem" class="form-label">Url Sistema</label>
                                <input disabled type="text" class="form-control" id="url_sistem" name="url_sistem"
                                    value="{{ old('url_sistem', $pageWeb->url_sistem ?: request()->getSchemeAndHttpHost()) }}"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label for="url_web" class="form-label">Url Pagina Web</label>
                                <input disabled type="text" class="form-control" id="url_web" name="url_web"
                                    value="{{ old('url_web', $pageWeb->url_web) }}" required>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ $pageWeb->url_web }}" target="_blank" class="btn icon btn-dark mt-4">
                                    <i class="fa-solid fa-globe fa-bounce"></i> Mi Pagina
                                </a>
                            </div>
                        </div>

                        <!-- Descripción del sitio -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea disabled class="form-control" id="descripcion" name="descripcion" rows="4" required>{{ old('descripcion', $pageWeb->descripcion) }}</textarea>
                            </div>
                        </div>

                        <!-- Billboards Section -->
                        <h5 class="mb-3 text-dark">Billboards:</h5>
                        @foreach ($pageWeb->billboards as $billboard)
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label for="img_url_{{ $billboard->id }}" class="form-label">Imagen URL (ID:
                                        {{ $billboard->id }})</label>
                                    <input disabled type="text" class="form-control"
                                        id="img_url_{{ $billboard->id }}" name="img_url[]"
                                        value="{{ old('img_url.' . $loop->index, $billboard->img_url) }}">
                                </div>

                                <!-- Botón para ver la imagen -->
                                @can('mostrar-billboard')
                                    <center>
                                        <div class="col-md-12 mt-3">
                                            <button type="button" class="btn icon btn-info" data-bs-toggle="modal"
                                                data-bs-target="#verModal-{{ $billboard->id }}">
                                                <i class="fas fa-eye"></i> Ver imagenes...
                                            </button>
                                        </div>
                                    </center>
                                @endcan

                                <!-- Modal de vista previa de la imagen -->
                                <div class="modal fade" id="verModal-{{ $billboard->id }}" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-white">Vista previa de la imagen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <center>
                                                <div class="modal-body">
                                                    <h6>Imagen subida local:</h6>
                                                    @if ($billboard->img_path)
                                                        <img src="{{ $billboard->getImageUrlAttribute() }}"
                                                            class="img-fluid mb-3" alt="Imagen almacenada">
                                                    @else
                                                        <p>No hay imagen cargada.</p>
                                                    @endif

                                                    <h6>Imagen de URL:</h6>
                                                    @if ($billboard->img_url)
                                                        <img src="{{ $billboard->img_url }}" class="img-fluid"
                                                            alt="Imagen desde URL">
                                                    @else
                                                        <p>No hay URL de imagen proporcionada.</p>
                                                    @endif
                                                </div>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </form>
                @endcan
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                $('#updateBillboardForm').submit(function(e) {
                    e.preventDefault();
                    // Mostrar el spinner al iniciar la solicitud
                    showSpinner();

                    var formData = new FormData(this); // Recoger todos los datos del formulario

                    $.ajax({
                        url: "{{ route('billboard.update', $pageWeb->id) }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                reproducirSonidoSuccess();
                                mostrarToast('success', response.message);
                                setTimeout(() => {
                                    // Recargar la página después de 1.5 segundos
                                    location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Hubo un error al actualizar la página.');
                        },
                        complete: function() {
                            // Ocultar el spinner cuando la solicitud finalice (éxito o error)
                            hideSpinner();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
