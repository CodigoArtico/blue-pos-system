<script>
    $(document).ready(function() {
        // Crear gráfico circular (doughnut) para Métodos de Pago
        var ctx = document.getElementById('metodosPagoChart').getContext('2d');
        var metodosPagoChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($metodosLabels) !!}, // Nombres de los métodos de pago
                datasets: [{
                    data: {!! json_encode($metodosData) !!}, // Cantidad de ventas por método
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FFC300',
                        '#DAF7A6'
                    ], // Colores de ejemplo
                    borderColor: ['#C70039', '#28B463', '#1F618D', '#FF5733',
                        '#C39BD3'
                    ], // Bordes
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
                cutoutPercentage: 70, // Hace el gráfico más delgado
            }
        });
    });
</script>