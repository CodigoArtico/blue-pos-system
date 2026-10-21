
<script>
    // Datos para el gráfico de clientes
    const labelsClientes = @json($clientNames); // Nombres de los clientes
    const ventasClientes = @json($clientTotalVentas); // Número de ventas
    const montoVentasClientes = @json($clientMontoVentas); // Monto total de ventas
    const puntosClientes = @json($clientPuntos); // Puntos calculados
    
    // Crear el gráfico de barras para clientes
    const ctxClientes = document.getElementById('clientesChart').getContext('2d');
    new Chart(ctxClientes, {
        type: 'bar',
        data: {
            labels: labelsClientes,
            datasets: [{
                    label: 'Ventas',
                    data: ventasClientes,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Monto Ventas (S/.)',
                    data: montoVentasClientes,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Puntaje',
                    data: puntosClientes,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>