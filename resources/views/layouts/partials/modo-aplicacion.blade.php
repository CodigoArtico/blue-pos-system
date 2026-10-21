<script>
    let deferredPrompt;
    const installButton = document.getElementById('install-btn'); // Botón de instalación
    const installMessage = document.getElementById('install-message'); // Contenedor del mensaje
    const rejectButton = document.getElementById('reject-btn'); // Botón de rechazo

    // Escuchar el evento beforeinstallprompt que indica que la app puede instalarse
    window.addEventListener('beforeinstallprompt', (event) => {
        // Prevenir que el navegador muestre su propio banner de instalación
        event.preventDefault();

        // Guardar el evento para mostrarlo más tarde
        deferredPrompt = event;

        // Hacer visible el mensaje y el botón de instalación
        installMessage.style.display = 'block';

        // Cuando el usuario haga clic en "Instalar"
        installButton.addEventListener('click', () => {
            // Mostrar el prompt de instalación personalizado
            deferredPrompt.prompt();

            // Esperar la respuesta del usuario (aceptar o rechazar)
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    // console.log('El usuario aceptó la instalación');
                } else {
                    // console.log('El usuario rechazó la instalación');
                }
                deferredPrompt = null; // Limpiar el evento
                installMessage.style.display = 'none'; // Ocultar el mensaje de instalación
            });
        });

        // Cuando el usuario haga clic en "Rechazar"
        rejectButton.addEventListener('click', () => {
            // console.log('El usuario rechazó la instalación');
            installMessage.style.display = 'none'; // Ocultar el mensaje de instalación
        });
    });

    // Instrucciones para iOS (Safari) sobre cómo agregar la app a la pantalla de inicio
    if (navigator.userAgent.match(/(iPad|iPhone|iPod)/g)) {
        alert(
            "Para agregar esta aplicación a tu pantalla de inicio, pulsa el botón de compartir en Safari y selecciona 'Agregar a la pantalla de inicio'."
            );
    }
</script>
<style>
    #install-message {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        z-index: 1000;
        display: none;
        width: 90%;
        max-width: 400px;
        text-align: center;
    }

    #install-btn {
        background-color: #00bcd4;
        color: #fff;
        font-size: 16px;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s ease;
    }

    #install-btn:hover {
        background-color: #008c9e;
    }

    #install-btn:active {
        background-color: #006f79;
    }

    /* Estilo para el botón de rechazo */
    #reject-btn {
        background-color: #ccc;
        color: #333;
        font-size: 16px;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
        margin-left: 10px;
        transition: background-color 0.3s ease;
    }

    #reject-btn:hover {
        background-color: #bbb;
    }

    #reject-btn:active {
        background-color: #888;
    }
</style>
