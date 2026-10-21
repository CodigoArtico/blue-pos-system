<x-app-layout>

    @section('title', 'Manual')

    @push('css')
        <style>
            .video-container {
                width: 100%;
                max-width: 1400px;
                height: 0;
                padding-bottom: 56.25%; /* Mantiene la proporción 16:9 */
                position: relative;
                margin-bottom: 20px; /* Espacio entre videos */
                display: flex;
                justify-content: center; /* Centrar el video */
            }

            .video-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: 0;
            }

            .card-body {
                margin-top: 0;
                padding-top: 0; /* Eliminar relleno superior si es necesario */
            }

            .card-body .card-text {
                margin-top: 0; /* Eliminar margen superior del texto */
            }

        </style>
    @endpush

    <div class="container-fluid">
        <div class="page-title">
            <h1 class="mt-4 text-center text-dark">Manual <i class="fa-solid fa-book-open-reader fa-lg"></i></h1>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manual</li>
                    </ol>
                </nav>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-content">
                <center>
                    <div class="card-body">
                        <h3 class="card-title mb-0 text-dark">Video Manual - v3.9</h3>
                    </div>
                    <div class="video-container">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/IRlcp8t8O-M?si=5fRxRssHJ6LUSrg3&rel=0" 
                        title="YouTube Video Sistema V3" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                    
                    <div class="video-container">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/vILUDdAEH2s?si=VRCCbGWb4El7U3DO&rel=0" 
                        title="YouTube Video Sistema V3" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>                    
                    <div class="card-body">
                        <p class="card-text">
                            En este video, te mostraremos cómo utilizar nuestro sistema de ventas e inventario de manera
                            efectiva...
                        </p>
                        <!-- Botón de Soporte técnico con icono de mail -->
                        <a href="mailto:codigoartico@gmail.com?subject=Sistema%20Inventario&body=Quiero%20información%20sobre%20el%20sistema."
                        class="btn btn-primary card-link mb-2 icon">
                        <i class="fas fa-envelope"></i> Soporte técnico
                        </a>

                        <!-- Botón de WhatsApp con icono de WhatsApp -->
                        <a title="Pronto" target="_blank" href="{{ !empty($licenciaUsuario->telf_soporte) ? 'https://wa.me/' . $licenciaUsuario->telf_soporte : config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) . '?text=' . urlencode('Hola, tengo una consulta sobre el manual del sistema') }}" class="btn btn-success icon card-link mb-2" aria-disabled="true">
                        <i class="fab fa-whatsapp"></i> Contactar...
                        </a>
                    </div>
                </center>
            </div>
        </div>
    </div>

    @push('js')
    @endpush
</x-app-layout>
