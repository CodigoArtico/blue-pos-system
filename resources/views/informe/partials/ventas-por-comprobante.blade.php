<script>
    $(document).ready(function() {
        // Gráfico circular de Ventas por Comprobantes
        var ctxComprobantes = document.getElementById('ventasComprobantesChart').getContext('2d');
        var ventasComprobantesChart = new Chart(ctxComprobantes, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($comprobanteLabels) !!}, // Nombres de los tipos de comprobantes
                datasets: [{
                    data: {!! json_encode($comprobanteData) !!}, // Cantidad de ventas por comprobante
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FFC300', '#DAF7A6'], // Colores
                    borderColor: ['#C70039', '#28B463', '#1F618D', '#FF5733', '#C39BD3'],
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