<script>
    // Función para ejecutar el comando
    function runCronCommand() {
        fetch('/run-verificar-baja')
            .then(response => response.json())
            .then(data => {
                console.log(data.status); // Muestra el estado en la consola
            })
            .catch(error => console.error('Error:', error));
    }

    // Ejecutar el comando cada 2 horas
    setInterval(runCronCommand, 7200000); // 7200000 ms = 2 horas
</script>

