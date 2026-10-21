<!-- Estilos para los spinners -->
<style>
    /* Estilo del spinner */
    .loading-spinner {
        border: 4px solid rgba(0, 0, 0, 0.644);
        border-top: 4px solid #00bcd4;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
    }

    /* Animación de rotación */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
            /* Comienza en 0 grados */
        }

        100% {
            transform: rotate(360deg);
            /* Rota hasta 360 grados */
        }
    }

    /* Estilo para el fondo del spinner */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        /* Ocupa todo el ancho de la pantalla */
        height: 100%;
        /* Ocupa todo el alto de la pantalla */
        background: rgba(0, 0, 0, 0.75);
        /* Fondo oscuro con opacidad */
        display: flex;
        /* Flexbox para centrar el contenido */
        align-items: center;
        /* Centrado vertical */
        justify-content: center;
        /* Centrado horizontal */
        z-index: 9999;
        /* Asegura que se muestre encima de todo el contenido */
    }
</style>

<!-- Spinner para la carga inicial de la vista -->
<div id="loading-screen" class="loading-screen" style="display: none;">
    <div class="loading-spinner"></div>
</div>

<!-- Spinner para la carga de DataTables -->
<div id="loading-screen-datatables" class="loading-screen" style="display: none;">
    <div class="loading-spinner"></div>
</div>

{{-- //recargar cuando el usuario regrese a las vistas pasadas --}}
<script>
    window.onload = function() {
        // Evita que la página se almacene en la caché
        window.history.forward();
    };
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
</script>
<!-- Script para mostrar y ocultar el spinner -->
<script>
    // Definir las funciones fuera del DOMContentLoaded para que estén disponibles globalmente
    function showSpinnerDataTables() {
        document.getElementById('loading-screen-datatables').style.display = 'flex';
    }

    function hideSpinnerDataTables() {
        document.getElementById('loading-screen-datatables').style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function() {
        const loadingScreen = document.getElementById('loading-screen');

        // Función para mostrar el spinner
        function showSpinner() {
            loadingScreen.style.display = 'flex';
        }

        // Función para ocultar el spinner
        function hideSpinner() {
            loadingScreen.style.display = 'none';
        }


        // Mostrar el spinner antes de que la página se recargue
        window.addEventListener('beforeunload', function() {
            showSpinner();
        });

        // Ocultar el spinner una vez que la página esté completamente cargada
        window.addEventListener('load', function() {
            hideSpinner();
        });

        // Ocultar el spinner cuando se muestra la página desde la caché del historial
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) { // Solo en caso de que la página se recargue desde la caché
                hideSpinner();
            }
        });

        // Exportar las funciones para que puedan ser llamadas manualmente
        window.showSpinner = showSpinner;
        window.hideSpinner = hideSpinner;

        window.showSpinnerDataTables = showSpinnerDataTables;
        window.hideSpinnerDataTables = hideSpinnerDataTables;
    });

    // como poner las funciones
    // Mostrar el spinner al iniciar la solicitud
    // window.showSpinner();
    //  .finally(() => {
    //                 // Ocultar el spinner cuando la solicitud finalice (éxito o error)
    //                 window.hideSpinner();
    //             });
</script>
