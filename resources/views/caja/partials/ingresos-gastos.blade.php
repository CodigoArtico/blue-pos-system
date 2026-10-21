<script>
    var ctx4 = document.getElementById('ingresosGastosChart').getContext('2d');

    // Definir los datos y opciones del gráfico
    var ingresosGastosChart = new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: ['Ingresos', 'Gastos'], // Etiquetas
            datasets: [{
                label: 'S/. (Soles)',
                data: [{{ $totalIngresos }}, {{ $totalGastos }}], // Totales de ingresos y gastos
                backgroundColor: ['#33FF57', '#FF5733'], // Colores: verde para ingresos, rojo para gastos
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Categoría'
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
                    display: false // Desactivar la leyenda por defecto
                },
            }
        }
    });

    // Función para actualizar el gráfico basado en los checkboxes
    function updateChart() {
        // Obtener los estados de los checkboxes
        var showIngresos = document.getElementById('toggleIngresos').checked;
        var showGastos = document.getElementById('toggleGastos').checked;

        // Modificar los datasets del gráfico
        ingresosGastosChart.data.datasets[0].data = [
            showIngresos ? {{ $totalIngresos }} : 0, // Mostrar ingresos si está marcado, si no poner 0
            showGastos ? {{ $totalGastos }} : 0  // Mostrar gastos si está marcado, si no poner 0
        ];

        // Actualizar el gráfico
        ingresosGastosChart.update();
    }

    // Añadir listeners a los checkboxes para que actualicen el gráfico al cambiar
    document.getElementById('toggleIngresos').addEventListener('change', updateChart);
    document.getElementById('toggleGastos').addEventListener('change', updateChart);

    // Llamar a la función de inicio para asegurar que el gráfico se cargue correctamente
    updateChart();
</script>