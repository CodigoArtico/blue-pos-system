<script>
    let previousOrderCount = localStorage.getItem('previousOrderCount') ? parseInt(localStorage.getItem(
        'previousOrderCount')) : 0; // Recupera del localStorage o inicializa en 0
    let hasUserInteracted = localStorage.getItem('hasUserInteracted') ===
        'true'; // Recupera del localStorage si el usuario ya ha interactuado

    // Función para reproducir sonido de notificación
    function reproducirSonidoNotification() {
        let audioElement = document.getElementById('audio-notification');
        if (audioElement) {
            audioElement.play()
                .then(() => {
                    console.log('Success: audio is playing.');
                })
                .catch(error => {
                    console.error('Error playing audio:', error);
                });
        } else {
            console.error('No se pudo encontrar el elemento de audio con ID "audio-notification".');
        }
    }

    // Función para obtener el número de órdenes desde el servidor
    function fetchOrderCount() {
        $.ajax({
            url: '{{ route('contar.ordenes') }}', // Usar la ruta nombrada de Laravel
            method: 'POST', // Cambiar a POST
            data: {
                _token: '{{ csrf_token() }}', // Token CSRF necesario para solicitudes POST en Laravel
                start_date: '{{ now()->format('Y-m-d') }}', // Fecha de inicio, por ejemplo hoy
                end_date: '{{ now()->format('Y-m-d') }}' // Fecha de fin, por ejemplo hoy
            },
            success: function(response) {
                const newOrderCount = response.orderCount;
                const hasPendingOrder = response.hasPendingOrder; // Recibimos la bandera

                // Actualiza la cantidad de pedidos en la UI
                $('.cantidad-pedidos').text(newOrderCount);

                // Actualiza el punto de notificación en el icono
                const notificationDot = $('.notification-dot-bell');

                if (newOrderCount > previousOrderCount || hasPendingOrder) {
                    // Si el contador de órdenes ha aumentado o si hay un pedido pendiente
                    if (hasPendingOrder) {
                        notificationDot.removeClass('notification-dot-blue'); // Elimina el color celeste
                        notificationDot.addClass('notification-dot-red'); // Agrega el color rojo
                    }
                    // Reproducir sonido si es un nuevo pedido y el usuario ha interactuado
                    if (hasUserInteracted) {
                        reproducirSonidoNotification();
                    }
                } else if (newOrderCount === 0) {
                    notificationDot.removeClass(
                    'notification-dot-blue notification-dot-red'); // Elimina el punto
                } else {
                    notificationDot.removeClass('notification-dot-red'); // Elimina el color rojo
                    notificationDot.addClass('notification-dot-blue'); // Agrega el color celeste
                }

                // Actualiza el contador de órdenes en localStorage
                previousOrderCount = newOrderCount;
                localStorage.setItem('previousOrderCount', newOrderCount);
            },

            error: function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
            }
        });
    }

    // Espera una interacción del usuario (solo la primera vez)
    $(document).on('click', function() {
        if (!hasUserInteracted) {
            // Marca que el usuario ha interactuado
            hasUserInteracted = true;
            localStorage.setItem('hasUserInteracted', 'true'); // Guarda en el localStorage
        }
    });

    // Llamar a la función al cargar la página y configurar intervalos
    $(document).ready(function() {
        fetchOrderCount(); // Llama a la función al cargar la página
        setInterval(fetchOrderCount, 10000); // Llama a esta función cada 10 segundos (10,000 ms)
    });
</script>
