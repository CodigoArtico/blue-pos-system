@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            reproducirSonidoSuccess();
            mostrarToast('success', '{{ session('success') }}');
        });
    </script>
@endif
@if (session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            reproducirSonidoAlert();
            mostrarToast('info', '{{ session('info') }}');
        });
    </script>
@endif
@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            reproducirSonidoError();
            mostrarToast('error', '{{ session('error') }}');
        });
    </script>
@endif
<script>
    // Función para mostrar toasts de SweetAlert2
    function mostrarToast(icon, title) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1500, // Mantenemos el tiempo corto
            timerProgressBar: true,
        });
        Toast.fire({
            icon,
            title
        });
    }

    function setTimeout1medio5sg(callback, tiempo = 1500) {
        setTimeout(() => {
            callback();
            location.reload(); // Recarga la página después de ejecutar el callback
        }, tiempo);

        // Ejemplo de cómo llamarlo
        // setTimeout1medio5sg(() => {
        //         console.log("Se ejecutó el callback y la página se recargará.");
        //     }, 1500);
    }
</script>
