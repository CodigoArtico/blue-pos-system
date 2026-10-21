<script>
    $(document).ready(function() {
        var ctx2 = document.getElementById('ventasGastosChart').getContext('2d');
        var ventasGastosChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($fechas) !!}, // Fechas en formato 'YYYY-MM-DD'
                datasets: [{
                        label: 'Ventas',
                        data: {!! json_encode($ventasPorDia) !!}, // Total de ventas por día
                        backgroundColor: '#33FF57',
                    },
                    {
                        label: 'Gastos',
                        data: {!! json_encode($gastosPorDia) !!}, // Total de gastos por día
                        backgroundColor: '#FF5733',
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            parser: 'YYYY-MM-DD',
                            unit: 'day',
                            tooltipFormat: 'DD-MM-YYYY'
                        },
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Monto (S/.)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': S/. ' + tooltipItem.raw
                                    .toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
