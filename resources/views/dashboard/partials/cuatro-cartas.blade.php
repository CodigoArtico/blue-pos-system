<script>
    // Datos para gráficos
    var fechasSaldo = @json($fechas);
    var totalesSaldo = @json($totales);

    var fechasProductos = @json($productosFechas);
    var totalesProductos = @json($productosTotales);

    var mesesVentas = @json($ventasMeses);
    var totalesVentas = @json($ventasTotales);

    var mesesSaldo = @json($saldoMeses);
    var totalesSaldoMensual = @json($saldoTotales);
    
    function toggleMonto() {
        var icono = document.getElementById("toggleMonto");
        var monto = document.getElementById("monto");

        // Si el monto está visible, ocultarlo y cambiar el icono a ojo cerrado
        if (monto.style.display === "block") {
            monto.style.display = "none";
            icono.classList.remove("fa-eye-slash");
            icono.classList.add("fa-eye");
        } else { // Si el monto está oculto, mostrarlo y cambiar el icono a ojo abierto
            monto.style.display = "block";
            icono.classList.remove("fa-eye");
            icono.classList.add("fa-eye-slash");
        }
    }
</script>

<div class="row mb-0">
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <h6 class="text-dark ms-2">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</h6>
                <div class="d-flex flex-column">
                    <div class='px-3 py-2 d-flex justify-content-between'>
                        <h3 class='card-title fs-5'>SALDO</h3>
                        <div class="card-right d-flex align-items-center">
                            <p class="fs-5">S/. {{ $totales[0] }}</p> <!-- Muestra el saldo actual -->
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvasSaldo" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <h6 class="text-dark ms-2">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</h6>
                <div class="d-flex flex-column">
                    <div class='px-3 py-2 d-flex justify-content-between'>
                        <h3 class='card-title fs-5'>N. PROD. VEND.</h3>
                        <div class="card-right d-flex align-items-center">
                            <p class="fs-5">{{ $productosTotales[0] }}</p> <!-- Muestra la cantidad de productos vendidos hoy -->
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvasProductos" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>       
    
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <h6 class="text-dark ms-2">Month</h6>
                <div class="d-flex flex-column">
                    <div class='px-3 py-2 d-flex justify-content-between'>
                        <h3 class='card-title fs-5'>VENT. POR MES.</h3>
                        <div class="card-right d-flex align-items-center">
                            <p class="fs-5">{{ $ventasTotales[0] }}</p> <!-- Muestra la cantidad de ventas en el mes actual -->
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvasVentas" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card card-statistic">
            <div class="card-body p-0">
                <h6 class="text-dark ms-2">Month</h6>
                <div class="d-flex flex-column">
                    <div class='px-3 py-2 d-flex justify-content-between'>
                        <h3 class='card-title fs-5'>SALDO POR MES</h3>
                        <div class="card-right d-flex align-items-center">
                            <!-- Mostrar el icono del ojo y vincularlo a la función para alternar la visibilidad del monto -->
                            <i class="fas fa-eye text-dark" id="toggleMonto" onclick="toggleMonto()" style="cursor: pointer;"></i>
                            <!-- Un elemento div con el monto oculto -->
                            <div id="monto" style="display:none; color: white">
                                S/. {{ $saldoTotales[0] }} <!-- Muestra el saldo actual -->
                            </div>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="canvasSaldoMensual" style="height:100px !important"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>