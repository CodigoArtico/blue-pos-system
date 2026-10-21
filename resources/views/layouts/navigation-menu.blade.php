<div class="sidebar-header">
    @php
        $extensionesValidas = ['webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'];
        $esImagenValida = false;

        if (!empty($imgAccount)) {
            $extension = pathinfo($imgAccount, PATHINFO_EXTENSION);
            $esImagenValida = in_array(strtolower($extension), $extensionesValidas);
        }
    @endphp
    <a href="{{ route('dashboard') }}" class="d-block">
        <img loading="lazy" rel="preload" src="{{ $esImagenValida ? $imgAccount : asset('images/banner2.png') }}" alt="Imagen de Empresa"
            class="img-fluid w-100 h-100">

    </a>
</div>

<div class="sidebar-menu">
    <ul class="menu" id="sidebar-menu">
        <div class="px-4 text-dark">
            <strong translate="no">-MENÚ</strong>
        </div>
        <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="/" class='sidebar-link'>
                <i class="fa-solid fa-house-fire"></i>
                <span translate="no">Dashboard</span>
            </a>

        </li>
        <!-- categorias -->
        @can('ver-categoria')
            <li class="sidebar-item {{ request()->routeIs('categorias.index') ? 'active' : '' }}">

                <a href="{{ route('categorias.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-folder-open"></i>
                    <span translate="no">Categorias</span>
                </a>
            </li>
        @endcan

        <!-- MARCAS -->
        @can('ver-marca')
            <li class="sidebar-item {{ request()->routeIs('marcas.index') ? 'active' : '' }}">

                <a href="{{ route('marcas.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-tags"></i>
                    <span translate="no">Marcas</span>
                </a>

            </li>
        @endcan

        <!-- PRESENTACIONES -->
        @can('ver-presentacion')
            <li class="sidebar-item {{ request()->routeIs('presentaciones.index') ? 'active' : '' }}">

                <a href="{{ route('presentaciones.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-layer-group"></i>
                    <span translate="no">Presentaciones</span>
                </a>

            </li>
        @endcan
        <!-- BILLBOARD -->
        @can('ver-billboard')
            <li class="sidebar-item {{ request()->routeIs('billboard.index') ? 'active' : '' }}">

                <a href="{{ route('billboard.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-pager fa-flip"></i>
                    <span>Billboard</span>
                </a>
                <span class="badge bg-info"
                    style="position: absolute; top: 5px; right: 10px; font-size: 0.75em; display: inline-block; padding: 0.2em 0.5em;">New</span>
            </li>
        @endcan
        <!-- PRODUCTO -->
        @can('ver-producto')
            <li class="sidebar-item {{ request()->routeIs('productos.index', 'productos.edit') ? 'active' : '' }}">

                <a href="{{ route('productos.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-shapes"></i>
                    <span translate="no">Productos</span>
                </a>

            </li>
        @endcan
        <!-- COMPRAS  -->
        @can('ver-compras')
            <li class="sidebar-item  {{ request()->routeIs('compras.index') ? 'active' : '' }}">

                <a href="{{ route('compras.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-money-bill-wheat"></i>
                    <span translate="no">Compras</span>
                </a>

            </li>
        @endcan
        <!-- VENTAS -->
        @can('ver-ventas')
            <li
                class="sidebar-item {{ request()->routeIs('ventas.index', 'ventas.create', 'ventas.show') ? 'active' : '' }}">

                <a href="{{ route('ventas.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-cart-plus fa-beat"></i>
                    <span translate="no">Ventas</span>
                </a>

            </li>
        @endcan
        <!-- CAJA Y GASTOS -->
        @if (auth()->user()->can('ver-caja') || auth()->user()->can('ver-gastos'))
            <li class="sidebar-item {{ request()->routeIs('caja.index') ? 'active' : '' }}">
                <a href="{{ route('caja.index') }}" class='sidebar-link'>
                    <i class="fas fa-cash-register"></i>
                    @if (auth()->user()->can('ver-caja') && auth()->user()->can('ver-gastos'))
                        <span translate="no">Caja / Gastos</span>
                    @elseif (auth()->user()->can('ver-caja'))
                        <span>Caja</span>
                    @elseif (auth()->user()->can('ver-gastos'))
                        <span>Gastos</span>
                    @endif
                </a>
                <span class="badge bg-info"
                    style="position: absolute; top: 5px; right: 10px; font-size: 0.75em; display: inline-block; padding: 0.2em 0.5em;">New</span>
            </li>
        @endif

        <!-- MESAS -->
        @can('ver-mesas')
            <li class="sidebar-item  {{ request()->routeIs('mesas.index', 'mesas.show') ? 'active' : '' }}">

                <a href="{{ route('mesas.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-table"></i>
                    <span>Mesas</span>
                </a>
                <!-- Etiqueta "New" ajustada al tamaño del texto -->
                <span class="badge bg-info"
                    style="position: absolute; top: 5px; right: 10px; font-size: 0.75em; display: inline-block; padding: 0.2em 0.5em;">New</span>
            </li>
        @endcan
        <!-- CLIENTES -->
        @can('ver-cliente')
            <li class="sidebar-item  {{ request()->routeIs('clientes.index') ? 'active' : '' }}">

                <a href="{{ route('clientes.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-users"></i>
                    <span translate="no">Clientes</span>
                </a>

            </li>
        @endcan

        <!-- PROVEEDORES -->
        @can('ver-proveedor')
            <li class="sidebar-item  {{ request()->routeIs('proveedores.index') ? 'active' : '' }}">

                <a href="{{ route('proveedores.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-truck"></i>
                    <span translate="no">Proveedores</span>
                </a>
            </li>
        @endcan

        <!-- INFORMES PRODUCTO  -->
        @can('informes')
            <li class="sidebar-item  {{ request()->routeIs('informe.index') ? 'active' : '' }}">

                <a href="{{ route('informe.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span translate="no">Informes</span>
                </a>
                <!-- Etiqueta "New" ajustada al tamaño del texto -->
                <span class="badge bg-info"
                    style="position: absolute; top: 5px; right: 10px; font-size: 0.75em; display: inline-block; padding: 0.2em 0.5em;">New</span>
            </li>
        @endcan
        {{-- EMPRESA  --}}
        @can('ver-empresa')
            <li class="sidebar-item  {{ request()->routeIs('empresas.index') ? 'active' : '' }}">

                <a href="{{ route('empresas.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-store"></i>
                    <span translate="no">Empresa</span>
                </a>
                <span class="badge bg-info"
                    style="position: absolute; top: 5px; right: 10px; font-size: 0.75em; display: inline-block; padding: 0.2em 0.5em;">New</span>
            </li>
        @endcan
        <!-- OTROS  -->
        @can('ver-users')
            <div class="px-4 text-dark">
                <strong translate="no">-ADMIN</strong>
            </div>
        @endcan

        <!-- USUARIOS -->
        @can('ver-users')
            <li class="sidebar-item  {{ request()->routeIs('users.index') ? 'active' : '' }}">

                <a href="{{ route('users.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-users"></i>
                    <span translate="no">Usuarios</span>
                </a>

            </li>
        @endcan
        <!-- ROLES  -->
        @can('ver-roles')
            <li class="sidebar-item  {{ request()->routeIs('roles.index') ? 'active' : '' }}">

                <a href="{{ route('roles.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-person-circle-plus"></i>
                    <span translate="no">Roles</span>
                </a>

            </li>
        @endcan
        @can('ver-ubicacion')
            <div class="px-4 text-dark">
                <strong translate="no">-MANUAL USER</strong>
            </div>


            <li class="sidebar-item  {{ request()->routeIs('manual.index') ? 'active' : '' }}">

                <a href="{{ route('manual.index') }}" class='sidebar-link'>
                    <i class="fa-solid fa-book-open-reader"></i>
                    <span translate="no">Manual</span>
                </a>
                <!-- Etiqueta "New" ajustada al tamaño del texto -->
                <span class="badge bg-info"
                    style="position: absolute; top: 5px; right: 10px; font-size: 0.75em; display: inline-block; padding: 0.2em 0.5em;">New</span>
            </li>
        @endcan

        <div class="text-center text-dark">
            <strong class='' translate="no">F@ Cod-Art</strong>
        </div>

    </ul>
</div>
