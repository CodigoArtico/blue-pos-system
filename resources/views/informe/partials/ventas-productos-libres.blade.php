<script>
    $(document).ready(function() {
        // Gráfico circular de Productos Libres
        var ctx = document.getElementById('productosLibresChart').getContext('2d');
        var productosLibresChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cantidad de Productos Libres Vendidos', 'Monto Total Vendido'], // Etiquetas para las dos partes
                datasets: [{
                    data: [
                        {!! json_encode($productoLibresCantidad) !!},  // Cantidad total de productos vendidos
                        {!! json_encode($productoLibresMontoVendido) !!} // Monto total vendido de productos libres
                    ],
                    backgroundColor: ['#FF5733', '#33FF57'],  // Colores diferenciados para cantidad y monto
                    borderColor: ['#C70039', '#28B463'],
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
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                },
                cutoutPercentage: 70, // Grosor del gráfico
            }
        });
    });
</script>