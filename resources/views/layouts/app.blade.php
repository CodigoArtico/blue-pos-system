<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta descripción general para SEO -->
    <meta name="description"
        content="{{ 'Sistema Multiventas e inventario ' . ($pageWeb->nombre ?? 'para tu negocio') . ' para gestionar tus ventas y emitir boletas y facturas gratuitamente. ¡Organiza tu negocio con facilidad!' }}">
    <meta name="author" content="Código Ártico">

    <title>{{ $pageWeb->nombre }} - @yield('title')</title>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'CodArt - Sistema de Ventas Multiventas - Punto de Venta Online Gratis')">
    <meta property="og:description"
        content="{{ 'Gestiona tus ventas e inventario, emite boletas y facturas de manera gratuita con ' . ($pageWeb->nombre ?? 'nuestro sistema') . '. Todo lo que necesitas para tu negocio en un solo lugar.' }}">
    <meta property="og:url" content="{{ $pageWeb->url_sistem }}">
    <meta property="og:image" content="@yield('og_image', isset($imgAccount) ? asset($imgAccount) : asset('images/banner2.png'))">
    <!-- Asegúrate de tener una imagen adecuada -->

    <!-- Data table incluimos npm-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/umd/simple-datatables.min.js"></script>

    <!-- Incluye Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Fonts Awesome -->
    <script src="https://kit.fontawesome.com/16454e4b22.js" crossorigin="anonymous"></script>

    <!-- Estilos adicionales -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Chart.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ isset($imgAccount) ? asset($imgAccount) : asset('images/favicon/favicon2.png') }}"
        type="image/x-icon">

    {{-- cargar modo applicacion --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Scripts -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('css')

</head>

<body translate="no">
    <div id="app">
        <div id="sidebar" class="active">

            <div class="sidebar-wrapper active">
                @include('layouts.navigation-menu')
                <button class="text-dark sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>

        <div id="main">
            @include('layouts.navigation-header')

            {{-- MODO APLICACION  --}}
            <script>
                // Registrar el Service Worker
                if ('serviceWorker' in navigator) {
                    window.addEventListener('load', function() {
                        navigator.serviceWorker.register('{{ asset('service-worker.js') }}')
                            .then(function(registration) {
                                // console.log('Service Worker registrado con éxito: ', registration);
                            })
                            .catch(function(error) {
                                // console.log('Error al registrar el Service Worker: ', error);
                            });
                    });
                }
            </script>
            <!-- Mensaje de instalación que aparecerá cuando sea necesario -->
            <div id="install-message" style="display:none;">
                <p>¡Agrega esta app a tu pantalla de inicio para un acceso rápido!</p>
                <button id="install-btn">Instalar</button>
                <!-- Botón de rechazo -->
                <button id="reject-btn">No ahora</button>
            </div>
            @include('layouts.partials.modo-aplicacion')
            {{-- MODO APLICACION  --}}


            {{-- INCLUIR SONIDO Y ALERTA DE RESPUESTA JSON  --}}
            @include('components.sound-alert.alert-view')
            @include('components.sound-alert.reproducir-sonido')
            {{-- @include('layouts.partials.disabled-inspeccion') --}}
            <!--MODAL PARA BUSCAR Y AÑADIR NUEVO CLIENTE -->
            @include('components.buscar-cliente-proveedor.modal-buscar-cliente')
            {{-- VERIFICAR BAJA DE EMISIONES --}}
            @include('components.verificar-baja.verificar-bajas-emisiones')
            {{-- SPINNER DE CARGA --}}
            @include('layouts.partials.spinner-carga')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Footer  --}}
        <footer>
            @include('layouts.navigation-footer')
        </footer>
    </div>

    {{-- moment.js carga  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script src="{{ asset('js/feather.min.js') }}"></script>
    <script src="{{ asset('js/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/choices.min.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/simple-datatables.js') }}"></script>
    <script src="{{ asset('js/vendors.js') }}"></script>

    <script src="{{ asset('js/main.js') }}"></script>
    @stack('js')
</body>

</html>
