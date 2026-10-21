
<script>
    // Datos para el gráfico de usuarios
    const labelsUsuarios = @json($userNames); // Nombres de los usuarios
    const ventasUsuarios = @json($userTotalVentas); // Número de ventas
    const montoVentasUsuarios = @json($userMontoVentas); // Monto total de ventas
    const puntosUsuarios = @json($userPuntos); // Puntos calculados
    
    // Crear el gráfico de barras para usuarios
    const ctxUsuarios = document.getElementById('usuariosChart').getContext('2d');
    new Chart(ctxUsuarios, {
        type: 'bar',
        data: {
            labels: labelsUsuarios,
            datasets: [{
                    label: 'Ventas',
                    data: ventasUsuarios,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Monto Ventas (S/.)',
                    data: montoVentasUsuarios,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Puntaje',
                    data: puntosUsuarios,
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
{{-- ------------------------------------------------ --}}