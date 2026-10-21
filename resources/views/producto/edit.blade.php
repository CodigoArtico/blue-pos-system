<x-app-layout>

    @section('title', 'Editar Producto')

    @push('css')
        <style>
            #descripcion {
                resize: none;
            }
        </style>
    @endpush

    <div class="container-fluid px-4">
        <h1 class="mt-4 text-center text-dark">Editar Producto <i class="fa-solid fa-shapes fa-lg"></i></h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item active">Editar producto</li>
        </ol>

        <div class="card text-bg-light">
            <form action="{{ route('productos.update', ['producto' => $producto]) }}" method="post"
                enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                <div class="card-body">

                    <div class="row g-4 text-dark">
                        <!----Codigo---->
                        {{-- <div class="col-md-1"> --}}
                        {{-- <label for="codigo" class="form-label">Código:</label> --}}
                        <input readonly type="hidden" name="codigo" id="codigo" class="form-control"
                            value="{{ old('codigo', $producto->codigo) }}">
                        @error('codigo')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                        {{-- </div> --}}

                        <!---Nombre---->
                        <div class="col-md-7">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                value="{{ old('nombre', $producto->nombre) }}">
                            @error('nombre')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>
                        <!-- stock -->
                        <div class="col-md-3">
                            <label for="stock" class="form-label">Stock:</label>
                            <input type="text" class="form-control" name="stock" id="stock"
                                value="{{ old('stock', $producto->stock) }}">
                            @error('stock')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>
                        <!-- Destacado -->
                        <div class="col-md-2">
                            <label for="destacado" class="form-label">Destacado:</label>
                            <div class="form-check">
                                <input type="checkbox" name="destacado" id="destacado" value="1"
                                    class="form-check-input text-primary"
                                    {{ old('destacado', $producto->destacado) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="destacado">
                                    Activar
                                </label>
                            </div>
                            @error('destacado')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>
                        <!---Descripción---->
                        <div class="col-md-6">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{ old('descripcion', $producto->descripcion) }}</textarea>
                            @error('descripcion')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>
                        <!-- Medida -->
                        <div class="col-md-3">
                            <label for="medida" class="form-label label-custom">Medida:
                                <span style="color:black; font-size: 0.8em;">
                                    (Añade separada por comas ",")
                                </span>
                            </label>
                            <input type="text" class="form-control" name="medida" id="medida"
                                placeholder="S,M,L,XL,2XL" value="{{ old('medida', $producto->medida) }}">
                        </div>
                        <!---Fecha de vencimiento---->
                        <div class="col-md-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de vencimiento:</label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control"
                                value="{{ old('fecha_vencimiento', $producto->fecha_vencimiento) }}">
                            @error('fecha_vencimiento')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>
                        <!-- Url img -->
                        <div class="col-md-8">
                            <label for="img_url" class="form-label label-custom">Url Img:</label>
                            <input type="text" class="form-control" name="img_url" id="img_url"
                                value="{{ old('img_url', $producto->img_url) }}">
                            @error('img_url')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>
                        <!--- Imagen ---->
                        <div class="col-md-4">
                            <label for="img_path" class="form-label">
                                Imagen:
                                <span style="color:black; font-size: 0.8em;">
                                    (mantendrá el actual si no selecciona nada)
                                </span> |
                                <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#verModal-{{ $producto->id }}">
                                    Ver imagen actual
                                </a>
                            </label>
                            <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
                            @error('img_path')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>


                        <!---Marca---->
                        <div class="col-md-4">
                            <label for="marca_id" class="form-label">Marca:</label>
                            <select data-size="4" title="Seleccione una marca" data-live-search="true"
                                name="marca_id" id="marca_id" class="choices form-control">
                                @foreach ($marcas as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $producto->marca_id == $item->id ? 'selected' : '' }}>{{ $item->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('marca_id')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>

                        <!---Presentaciones---->
                        <div class="col-md-4">
                            <label for="presentacione_id" class="form-label">Presentación:</label>
                            <select data-size="4" title="Seleccione una presentación" data-live-search="true"
                                name="presentacione_id" id="presentacione_id" class="choices form-control">
                                @foreach ($presentaciones as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $producto->presentacione_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}</option>
                                @endforeach
                            </select>
                            @error('presentacione_id')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>

                        <!---Categorías---->
                        <div class="col-md-4">
                            <label for="categorias" class="form-label">Categorías:</label>
                            <select required class="choices form-select select-light-danger" multiple="multiple"
                                title="Seleccione las categorías" name="categorias[]" id="categorias" multiple>
                                @foreach ($categorias as $item)
                                    <option value="{{ $item->id }}"
                                        {{ in_array($item->id, $producto->categorias->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $item->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categorias')
                                <small class="text-danger">{{ '*' . $message }}</small>
                            @enderror
                        </div>

                        <!---Precios de Compra y Venta de la Compra Más Reciente---->
                        @if ($compraReciente)
                            <div class="col-12">
                                <h5 class="text-dark">Precios de Compra y Venta de la Compra Más Reciente</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="precio_compra_{{ $compraReciente->id }}"
                                            class="form-label">Precio de Compra
                                            ({{ $compraReciente->created_at->format('d/m/Y') }}):</label>
                                        <input type="number" step="0.01"
                                            name="precios_compra[{{ $compraReciente->id }}]"
                                            id="precio_compra_{{ $compraReciente->id }}" class="form-control"
                                            value="{{ old('precios_compra.' . $compraReciente->id, $compraReciente->pivot->precio_compra) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="precio_venta_{{ $compraReciente->id }}" class="form-label">Precio
                                            de Venta ({{ $compraReciente->created_at->format('d/m/Y') }}):</label>
                                        <input type="number" step="0.01"
                                            name="precios_venta[{{ $compraReciente->id }}]"
                                            id="precio_venta_{{ $compraReciente->id }}" class="form-control"
                                            value="{{ old('precios_venta.' . $compraReciente->id, $compraReciente->pivot->precio_venta) }}">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk me-2"></i>Guardar Cambios</button>
                    {{-- <button type="reset" class="btn btn-secondary">Reiniciar</button> --}}
                </div>
            </form>
        </div>
    </div>
    <!-- Modal vista previa -->
    <div class="modal fade" id="verModal-{{ $producto->id }}" tabindex="-1"
        aria-labelledby="verModalLabel-{{ $producto->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="verModalLabel-{{ $producto->id }}">Imagen Actual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    @if ($producto->img_path)
                        <img src="{{ asset('storage/productos/' . $producto->img_path) }}" class="img-fluid"
                            alt="Imagen actual de {{ $producto->nombre }}">
                    @else
                        <p>No hay una imagen disponible para este producto.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('js')
    @endpush


</x-app-layout>
