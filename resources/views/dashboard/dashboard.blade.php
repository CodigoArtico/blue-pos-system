<x-app-layout>
    @section('title', 'Dashboard')

    @push('css')
    @endpush

    @can('ver-panel-principal')
        <div class="main-cantent container-fluid px-4">
            <div class="page-title">
                @can('super-boton-obtener-sistema')
                    @include('components.obtener-sistema.obtener')
                @endcan
                <h3 class="text-dark">Panel de Administración</h3>
                <p class="text-dark">
                    Aquí podrás observar las estadísticas de tu tienda.
                <div class="text-dark alert alert-secondary alert-dismissible fade show text-center page-title">
                    <span>Si eres nuevo, échale un vistazo al<a href="{{ route('manual.index') }}" class="text-dark"><strong>
                                manual</strong></a>.
                        <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </span>
                </div>
                </p>
            </div>

            <section class="section">
                
                {{-- CUATRO CARTAS CON ESTADISTICAS  --}}
                @include('dashboard.partials.cuatro-cartas')

                {{-- ENLACES DIRECTOS A FUNCIONALIDADES DEL SISTEMA  --}}
                @include('dashboard.partials.enlaces-directos')

                <div class="row mb-1">
                    <div class="col-md-8">
                        {{-- GRAFICO DE PRODUCTOS VENDIDOS POR MESES  --}}
                        @include('dashboard.partials.productos-mas-vendidos')

                        {{-- TOP DE MEJORES VENDEDORES  --}}
                        @include('dashboard.partials.top-vendedores')

                        {{-- VENTAS RECIENTES  --}}
                        @include('dashboard.partials.ventas-recientes')
                    </div>

                    <div class="col-md-4">
                        {{-- VENTAS POR METODO DE PAGO  --}}
                        @include('dashboard.partials.metodo-pago')
                        {{-- TOP DE CLIENTES  --}}
                        @include('dashboard.partials.top-clientes')
                        {{-- STOCK CRITICO DE PRODUCTOS  --}}
                        @include('dashboard.partials.stock-critico')
                    </div>
                </div>

            </section>

        </div>
    @else
        <div class="main-cantent container-fluid px-4">
            <p class="text-dark">Si Necesitas ver el Panel comunicate con el administrador.</p>
            @can('super-boton-obtener-sistema')
                @include('components.obtener-sistema.obtener')
            @endcan
        </div>
    @endcan
    @push('js')
    @endpush

</x-app-layout>
