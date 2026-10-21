<script>
    $(document).ready(function() {
        // Crear gráfico circular (doughnut) de Ganancia y Ventas Totales
        var ctx = document.getElementById('gananciaVentasChart').getContext('2d');
        var gananciaVentasChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Ganancia Final', 'Ventas Totales Eliminadas', 'Monto Venta Total'], // Nuevas etiquetas
                datasets: [{
                    data: [
                        {{ $gananciaFinal }},
                        {{ $ventasTotalesEliminadas }},
                        {{ $montoVentaTotal }}  // Suma de ventas de productos normales + productos libres
                    ],
                    backgroundColor: ['#2E86C1', '#E74C3C', '#4CAF50'], // Azul intenso, Rojo coral, Verde brillante
                    borderColor: ['#1F618D', '#C0392B', '#388E3C'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'S/. ' + tooltipItem.raw.toFixed(2); // Monto en formato con "S/"
                            }
                        }
                    }
                },
                cutoutPercentage: 70, // Hace el gráfico más delgado
            }
        });
    });
</script>