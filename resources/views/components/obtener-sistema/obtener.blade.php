<style>
    /* Estilos para el contenedor principal */
    .button-row {
        display: flex; /* Alineación horizontal de las columnas */
        justify-content: center; /* Centrar contenido horizontalmente */
        gap: 10px; /* Espaciado entre las columnas */
        text-align: center; /* Centrar texto dentro de cada columna */
    }

     /* Columnas individuales (texto + botón) */
     .button-column {
        display: flex;
        flex-direction: column; /* Alineación vertical dentro de la columna */
        align-items: center; /* Centrar contenido horizontalmente */
    }

    /* Ajustar estilo de los textos */
    .button-column strong {
        display: block;
        margin: 0; /* Eliminar márgenes */
        padding: 0; /* Eliminar relleno */
        line-height: 1; /* Ajustar la altura de línea */
        font-size: 14px; /* Ajustar el tamaño de fuente si es necesario */
    }

    /* Estilos generales para los botones */
    .btn-obtener {
        border-radius: 8px; /* Bordes redondeados */
        padding: 5px 10px; /* Espaciado interno */
        font-size: 16px; /* Tamaño de fuente accesible */
        transition: background-color 0.3s ease, transform 0.2s ease; /* Animación suave */
        /* color: white; Texto blanco */
        border: none; /* Sin borde */
        cursor: pointer; /* Manito al pasar */
    }

    /* Estilos específicos para cada botón */
    .btn-obtener-sistema {
        background-color: #52cf59; /* Verde suave y moderno */
    }

    .btn-obtener-sistema:hover {
        background-color: #45A049; /* Verde ligeramente más oscuro */
        transform: scale(1.05); /* Efecto de zoom */
    }

    .btn-soy-estudiante {
        background-color: #5C6BC0; /* Azul moderno */
    }

    .btn-soy-estudiante:hover {
        background-color: #3F51B5; /* Azul más profundo */
        transform: scale(1.05); /* Efecto de zoom */
    }

    /* Estilo para el texto fuerte encima de los botones */
    .button-column strong {
        display: block; /* Asegura que ocupe una línea completa */
        margin-bottom: 3px; /* Espaciado entre el texto y el botón */
    }
</style>
{{-- <h6 class="text-center">Elige una de las siguientes opciones</h6> --}}

<div class="button-row">
    <!-- Columna 1: Botón "Obtener sistema" -->
    <div class="button-column">
        <strong class="text-dark">Cuenta personal</strong>
        <button class="btn-obtener btn-obtener-sistema">
            <i class="fa-brands fa-whatsapp"></i>
            <a href="{{ !empty($licenciaUsuario->telf_soporte) ? 'https://wa.me/' . $licenciaUsuario->telf_soporte . '?text=Quiero%20el%20sistema' : config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) . '?text=' . urlencode('Hola, quiero adquirir el sistema') }}" target="_blank" style="color: white; text-decoration: none;">Whatsapp</a>
        </button>
    </div>

    <!-- Columna 2: Botón "Soy Estudiante" -->
    {{-- <div class="button-column">
        <strong class="text-dark">Estudiante?</strong>
        <button class="btn-obtener btn-soy-estudiante">
            <i class="fa-solid fa-user-graduate"></i>
            <a href="{{ !empty($licenciaUsuario->telf_soporte) ? 'https://wa.me/' . $licenciaUsuario->telf_soporte . '?text=Soy%20estudiante%20necesito%20el%20sistema' : config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) . '?text=' . urlencode('Hola, soy estudiante y necesito el sistema') }}" target="_blank" style="color: white; text-decoration: none;">Estudiante</a>
        </button>
    </div> --}}

    <!-- Columna 3: Botón "Obtener Nueva Version" -->
    <div class="button-column">
        <strong class="text-dark">Nueva Version</strong>
        <button class="btn-obtener btn-soy-estudiante">
            <i class="fa-solid fa-circle-arrow-up fa-flip"></i>
            <a href="https://pe.codart.cgrt.net" target="_blank" style="color: white; text-decoration: none;">Obtener</a>
        </button>
    </div>
</div>

{{-- <button class="btn btn-dark icon">
    <i class="fa-solid fa-envelope"></i> <!-- Icono de Gmail -->
    <a href="mailto:codigoartico@gmail.com?subject=Sistema%20Inventario&body=Quiero%20información%20sobre%20el%20sistema." class="card-link">Obtener Sistema</a>
</button> --}}
