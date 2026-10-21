@include('layouts.partials.css-navigation-header')
@include('layouts.partials.js-navigation-header')

<nav class="navbar navbar-header navbar-expand navbar-light">
    <a class="sidebar-toggler" href="#"><span class="navbar-toggler-icon"></span></a>
    <button class="btn navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex align-items-center navbar-light ms-auto">
            <!-- Ícono de música con dropdown -->
            <li class="nav-item dropdown position-relative me-1">
                <a href="#" id="toggle-music" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="d-lg-inline-block position-relative">
                        <i class="fa-solid fa-music fs-4 text-dark"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-web p-2" style="width: 250px; right: -50px;">
                    <h6 class="text-center">Reproductor</h6>
                    <input type="range" id="seek-bar" class="form-range" min="0" max="100" value="0"
                        step="1">
                    <div class="d-flex justify-content-between">
                        <small id="current-time">0:00</small>
                        <small id="total-duration">0:00</small>
                    </div>
                    <div class="text-center mt-2">
                        <button id="play-pause-btn" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                </div>
            </li>

            <!-- Iframe oculto para reproducir el audio -->
            <div id="player"></div>

            <script>
                let player;
                let isPlaying = false;
                let duration = 0;

                // Cargar la API de YouTube
                var tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                function onYouTubeIframeAPIReady() {
                    // console.log("API de YouTube cargada");

                    player = new YT.Player('player', {
                        height: '0',
                        width: '0',
                        videoId: '-b-9zGeKQok', // ID del video
                        playerVars: {
                            autoplay: 0,
                            controls: 0,
                            showinfo: 0
                        },
                        events: {
                            'onReady': function(event) {
                                // console.log("YouTube Player listo");

                                duration = player.getDuration();
                                document.getElementById("total-duration").innerText = formatTime(duration);

                                // Recuperar progreso guardado
                                let savedTime = localStorage.getItem("musicProgress");
                                if (savedTime) {
                                    // console.log("Recuperando progreso guardado:", savedTime);
                                    player.seekTo(parseFloat(savedTime), true);
                                }

                                // Recuperar estado de reproducción
                                let wasPlaying = localStorage.getItem("musicIsPlaying") === "true";

                                if (!wasPlaying) {
                                    // console.log("La música estaba en pausa antes de recargar, manteniendo en pausa.");
                                    isPlaying = false;
                                    document.getElementById("play-pause-btn").innerHTML =
                                        '<i class="fa-solid fa-play"></i>';
                                    player.pauseVideo(); // Asegurar que no se reproduzca automáticamente
                                }
                            },
                            'onStateChange': function(event) {
                                if (event.data == YT.PlayerState.PLAYING) {
                                    isPlaying = true;
                                    document.getElementById("play-pause-btn").innerHTML =
                                        '<i class="fa-solid fa-pause"></i>';
                                    updateSeekBar();
                                } else {
                                    isPlaying = false;
                                    document.getElementById("play-pause-btn").innerHTML =
                                        '<i class="fa-solid fa-play"></i>';
                                }

                                // Guardar estado de reproducción
                                localStorage.setItem("musicIsPlaying", isPlaying);
                            },
                            'onError': function(event) {
                                console.error("Error en YouTube Player", event);
                            }
                        }
                    });
                }

                function updateSeekBar() {
                    if (!isPlaying || !player) return; // Solo actualizar si está reproduciendo

                    let currentTime = player.getCurrentTime();
                    document.getElementById("seek-bar").value = (currentTime / duration) * 100;
                    document.getElementById("current-time").innerText = formatTime(currentTime);

                    // Guardar el progreso en localStorage
                    localStorage.setItem("musicProgress", currentTime);

                    // Llamar a la función en el siguiente frame
                    requestAnimationFrame(updateSeekBar);
                }

                // Iniciar la actualización cuando se reproduzca la música
                document.getElementById('play-pause-btn').addEventListener('click', function() {
                    if (player) {
                        if (isPlaying) {
                            player.pauseVideo();
                        } else {
                            player.playVideo();
                            requestAnimationFrame(updateSeekBar); // Iniciar el loop solo cuando se reproduce
                        }
                    }
                });


                // Controlar el avance de la música con el slider
                document.getElementById('seek-bar').addEventListener('input', function() {
                    if (player) {
                        let seekTo = (this.value / 100) * duration;
                        player.seekTo(seekTo, true);
                        // console.log("Saltando a:", seekTo);
                    }
                });

                // Formatear tiempo en minutos:segundos
                function formatTime(seconds) {
                    let min = Math.floor(seconds / 60);
                    let sec = Math.floor(seconds % 60);
                    return min + ":" + (sec < 10 ? "0" : "") + sec;
                }

                // Evitar que la música se pause al cambiar de pestaña
                document.addEventListener("visibilitychange", function() {
                    if (document.visibilityState === "hidden" && isPlaying) {
                        // console.log("Pestaña en segundo plano, reintentando reproducción...");
                        setTimeout(() => {
                            if (isPlaying) {
                                player.playVideo();
                            }
                        }, 1000);
                    }
                });

                // Asegurar que si el usuario pausó antes de recargar, la música no se reproduzca automáticamente
                window.addEventListener("load", function() {
                    let wasPlaying = localStorage.getItem("musicIsPlaying") === "true";
                    if (!wasPlaying) {
                        // console.log("La música estaba en pausa antes de recargar, manteniendo en pausa.");
                        isPlaying = false;
                        document.getElementById("play-pause-btn").innerHTML = '<i class="fa-solid fa-play"></i>';
                    }
                });
            </script>


            <li class="dropdown nav-icon position-relative" title="sitio web">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg">
                    <div class="d-lg-inline-block position-relative">
                        <i class="fa-solid fa-globe fa-bounce fs-4"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-web">
                    <div class="list-group-item border-0 align-items-start d-flex">
                        <a target="_blank" class="dropdown-item rounded" href="{{ $pageWeb->url_web }}">
                            <i class="fa fa-link"></i> Mi Página
                        </a>
                    </div>
                </div>
            </li>

            <li class="dropdown nav-icon position-relative" title="notificacion">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg">
                    <div class="d-lg-inline-block position-relative">
                        <i data-feather="bell"></i>
                        <span class="notification-dot-bell notification-dot-blue"></span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-large">
                    {{-- <h6 class="py-2 px-4 text-bold text-dark">Notificaciones </h6> --}}
                    <ul class="list-group rounded-none">
                        <li class="list-group-item border-0 align-items-start d-flex">
                            <div class="avatar-ordenes bg-success me-1">
                                <span class="avatar-content"><i data-feather="shopping-cart"></i></span>
                            </div>
                            <div class="text-center">
                                <h6 class="text-semibold text-dark">Pedidos Online<i
                                        class="fa-solid fa-spinner fa-spin-pulse ms-2"></i></h6>
                                @can('admin-pedidos')
                                    <p class="text-xs text-dark">Cant. Hoy: <span class="cantidad-pedidos"></span></p>

                                    <p>
                                        <a class="dropdown-item active rounded bg-dark" href="{{ route('orden.index') }}">
                                            <i class="fa fa-arrow-circle-right"></i> Ir a Pedidos
                                        </a>
                                    </p>
                                @endcan
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="dropdown nav-icon position-relative" title="mensajes">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg">
                    <div class="d-lg-inline-block position-relative">
                        <i data-feather="mail"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-menu">
                    <!-- Mostrar el componente "obtener-sistema" si tiene el permiso -->

                    @include('components.obtener-sistema.obtener')
                    <hr>
                    <div class="px-3 py-2 text-center">
                        <p class="mb-0 text-muted">Sin mensajes</p>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <div class="avatar me-1">
                        <img src="{{ Auth::user() && Auth::user()->img
                            ? (filter_var(Auth::user()->img, FILTER_VALIDATE_URL)
                                ? Auth::user()->img
                                : asset('storage/img_user/' . Auth::user()->img))
                            : asset('images/profile1.png') }}"
                            alt="Imagen de perfil" class="img-thumbnail" style="width: 35px; height: 35px;">
                    </div>
                    <div class="d-none d-md-block d-lg-inline-block">Hola, {{ Auth::user()->name }}</div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    @can('ver-perfil')
                        <a class="dropdown-item" href="{{ route('perfil.index') }}"><i data-feather="user"></i>
                            Cuenta</a>
                        <div class="dropdown-divider"></div>
                    @endcan
                    <!-- Formulario de cierre de sesión -->
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i data-feather="log-out"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
@push('js')
@endpush
