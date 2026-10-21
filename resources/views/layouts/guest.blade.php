<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta descripción para SEO -->
    <meta name="description"
    content="{{ 'Sistema Multiventas e inventario ' . ($pageWeb->nombre ?? 'para tu negocio') . ' para gestionar tus ventas y emitir boletas y facturas gratuitamente. ¡Organiza tu negocio con facilidad!' }}">
    <meta name="author" content="Código Ártico">

    <title>{{ $pageWeb->nombre }} - @yield('title')</title>

    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'CodArt - Sistema de Ventas Multiventas - Punto de Venta Online Gratis')">
    <meta property="og:description"
    content="{{ 'Gestiona tus ventas e inventario, emite boletas y facturas de manera gratuita con ' . ($pageWeb->nombre ?? 'nuestro sistema') . '. Todo lo que necesitas para tu negocio en un solo lugar.' }}">
    <meta property="og:url" content="{{ $pageWeb->url_sistem }}">
    <meta property="og:image" content="@yield('og_image', isset($imgAccount) ? asset($imgAccount) : asset('images/banner2.png'))">
    <!-- Asegúrate de tener una imagen adecuada -->

    <script src="https://kit.fontawesome.com/16454e4b22.js" crossorigin="anonymous"></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ isset($imgAccount) ? asset($imgAccount) : asset('images/favicon/favicon2.png') }}"
        type="image/x-icon">

</head>

<body>
    {{-- SPINNER DE CARGA --}}
    @include('layouts.partials.spinner-carga')

    {{ $slot }}

</body>

</html>
