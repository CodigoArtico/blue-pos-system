<script>
    // Desactivar clic derecho
document.addEventListener('contextmenu', (e) => {
    e.preventDefault();
    // alert("El clic derecho está deshabilitado.");
});

// Desactivar atajos de teclado comunes
document.addEventListener('keydown', (e) => {
    // Desactivar Ctrl + S (Guardar página)
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        // alert("Guardar página está deshabilitado.");
    }

    // Desactivar Ctrl + U (Ver código fuente)
    if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
        e.preventDefault();
        // alert("Ver código fuente está deshabilitado.");
    }

    // Desactivar F12, Ctrl + Shift + I, Ctrl + Shift + J, Ctrl + Shift + C
    if (
        e.key === 'F12' ||
        ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C'))
    ) {
        e.preventDefault();
        // alert("Acceso a las herramientas de desarrollo está deshabilitado.");
    }
});

// Detectar apertura de herramientas de desarrollo
(function() {
    let devtools = /./;
    devtools.toString = function() {
        this.opened = true;
    };

    setInterval(function() {
        if (devtools.opened) {
            // alert("Herramientas de desarrollo abiertas. Acceso bloqueado.");
            window.location.href = '/'; // Redirige a la página principal o muestra un mensaje
            devtools.opened = false; // Reinicia el estado
        }
    }, 1000); // Verifica cada segundo
})();

// Bloquear la consola de desarrollador
(function() {
    const originalConsole = console;
    console = {};
    Object.keys(originalConsole).forEach((key) => {
        console[key] = function() {
            // alert("El uso de la consola está deshabilitado.");
        };
    });
})();
</script>