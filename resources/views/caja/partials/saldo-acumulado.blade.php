<script>
    $(document).ready(function() {
        var ctx3 = document.getElementById('saldoAcumuladoChart').getContext('2d');
        var saldoAcumuladoChart = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: {!! json_encode($fechas) !!}, // Fechas en formato 'YYYY-MM-DD'
                datasets: [{
                    label: 'Saldo Total Acumulado',
                    data: {!! json_encode($saldoAcumuladoPorDia) !!}, // Saldo acumulado por día
                    borderColor: '#3357FF',
                    backgroundColor: 'rgba(51, 87, 255, 0.2)',
                    fill: true,
                }]
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
                                return 'S/. ' + tooltipItem.raw.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
