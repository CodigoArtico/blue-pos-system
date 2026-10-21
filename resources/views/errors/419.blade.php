<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 Sesion Expired</title>
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
    <script src="https://kit.fontawesome.com/16454e4b22.js" crossorigin="anonymous"></script>
    <style>
        /* Aquí va tu estilo personalizado para la página 404 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body,
        html {
            width: 100%;
            height: 100%;
            font-family: Arial, sans-serif;
            color: white;
        }

        /* Video de fondo */
        #video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            display: none;
            /* Inicialmente oculto */
        }

        /* Imagen de respaldo */
        #image-fallback {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            display: none;
        }

        /* Contenedor principal */
        #error {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            background: rgba(0, 0, 0, 0.5);
            /* Fondo semitransparente para mejorar la visibilidad */
        }

        /* Título y texto */
        .error-title {
            font-size: 6rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Botón */
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Video de fondo -->
    

    <!-- Imagen de respaldo que aparece antes de que el video se cargue -->
    <img id="image-fallback" src="{{ asset('images/login1.webp') }}" alt="Imagen de respaldo" />

    <!-- Contenido principal -->
    <div id="error">
        <h1 class="error-title">419</h1>
        <p>Oops, tu sesión ha expirado.</p>
        <a href="{{ '/' }}" class="btn"><i class="fa-solid fa-house-circle-check"></i> Inicio</a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Copyright &copy; CodArt 2024</p>
    </div>

    <!-- Script para manejar la lógica del video y la imagen de respaldo -->
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video-background');
            const fallbackImage = document.getElementById('image-fallback');

            // Verificamos si el dispositivo es móvil (ancho menor o igual a 768px)
            if (window.innerWidth <= 768) {
                // En móviles, solo mostramos la imagen de fondo, no intentamos cargar el video
                video.style.display = 'none';
                fallbackImage.style.display = 'block';
                return; // No hacemos nada más
            }

            // Si estamos en escritorio, verificamos la conexión a internet (solo funciona en navegadores que soportan Network Information API)
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            const slowConnectionThreshold = 1.5; // Umbral de velocidad de conexión en Mbps
            const isSlowConnection = connection && connection.downlink && connection.downlink <
                slowConnectionThreshold;

            // Función que oculta el video y muestra la imagen de fondo
            function showImageFallback() {
                video.style.display = 'none';
                fallbackImage.style.display = 'block';
            }

            // Si la conexión es lenta, mostramos solo la imagen y evitamos cargar el video
            if (isSlowConnection) {
                showImageFallback();
                return; // No intentamos cargar el video
            }

            // En otros casos, intentamos cargar el video
            const videoSource = video.querySelector('source');
            const videoUrl = videoSource.getAttribute('src');

            // Tiempo máximo de espera para el video (5 segundos)
            const videoLoadTimeout = setTimeout(function() {
                showImageFallback(); // Si el video no carga en 5 segundos, mostramos la imagen
            }, 5000);

            // Intentamos cargar el video desde el cache
            const isVideoInCache = caches.match(videoUrl).then(function(response) {
                if (response) {
                    // El video está en cache, lo mostramos
                    video.style.display = 'block';
                    video.style.opacity = 1;
                    video.play();
                    clearTimeout(videoLoadTimeout); // Si el video se carga a tiempo, cancelamos el timeout
                } else {
                    // El video no está en cache, lo cargamos normalmente
                    video.style.opacity = 0; // Lo dejamos invisible mientras se carga
                    video.style.display = 'block'; // Mostramos el video
                    video.addEventListener('loadeddata', function() {
                        video.style.opacity = 1; // Lo hacemos visible cuando se cargue
                        video.play();
                        clearTimeout(
                        videoLoadTimeout); // Cancelamos el timeout si el video se carga a tiempo
                    });
                    video.addEventListener('error', function() {
                        console.error("Error al cargar el video.");
                        showImageFallback();
                    });
                }
            }).catch(function() {
                showImageFallback(); // Si falla la verificación del cache, mostramos la imagen
            });
        });
    </script>
</body>

</html>
